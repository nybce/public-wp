<?php

namespace Wpo\Sync;

use WP_Error;
use \Wpo\Core\Domain_Helpers;
use \Wpo\Core\Extensions_Helpers;
use \Wpo\Core\Permissions_Helpers;
use \Wpo\Core\Url_Helpers;
use \Wpo\Core\WordPress_Helpers;
use Wpo\Core\Wpmu_Helpers;
use \Wpo\Services\Graph_Service;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;
use \Wpo\Services\Request_Service;
use \Wpo\Services\User_Create_Update_Service;
use \Wpo\Services\User_Service;
use \Wpo\Sync\Sync_Db;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Sync\SyncV2_Service')) {

    class SyncV2_Service
    {

        /**
         * Will schedule user synchronization using the schedule configured by the user.
         * 
         * @since 15.0
         * 
         * @return boolean|WP_Error
         */
        public static function schedule($job_id)
        {

            $job = self::get_user_sync_job_by_id($job_id);

            if (is_wp_error($job)) {
                return $job;
            }

            // Delete all scheduled jobs
            self::get_scheduled_events($job_id, true);

            try {

                if (!isset($job['schedule']) || !isset($job['schedule']['scheduledOn']) || !isset($job['schedule']['scheduledAt'])) {
                    throw new \Exception('Too few arguments to schedule user synchronization job were supplied');
                }

                $now = time();
                $day_of_the_week = intval(date('N', $now));
                $sel_day_of_the_week = intval(intval($job['schedule']['scheduledOn']));
                $hours_of_the_day = intval(date('H', $now));
                $sel_hours_of_the_day = intval(intval($job['schedule']['scheduledAt']));
                $recurrence = $sel_day_of_the_week < 7 ? 'wpo_weekly' : 'wpo_daily';
                $diff_days = 0;
                $diff_hours = 0;

                $seconds_in_an_hour = 60 * 60;
                $seconds_in_a_day = 24 * $seconds_in_an_hour;
                $treshold = 0;

                if ($sel_day_of_the_week < 7) {

                    if ($sel_day_of_the_week > $day_of_the_week) {
                        $diff_days = $sel_day_of_the_week - $day_of_the_week;
                    }

                    if ($sel_day_of_the_week < $day_of_the_week) {
                        $diff_days = (7 + $sel_day_of_the_week - $day_of_the_week);
                    }
                }

                if ($sel_hours_of_the_day > 0 && $sel_hours_of_the_day != $hours_of_the_day) {
                    $diff_hours = $sel_hours_of_the_day - $hours_of_the_day;
                }

                if ($diff_days === 0 && $sel_hours_of_the_day > 0 && $sel_hours_of_the_day < $hours_of_the_day) {
                    $diff_days = $sel_day_of_the_week == 7 ? 1 : 7;
                }

                $first_time = time() + ($diff_days * $seconds_in_a_day) + ($diff_hours * $seconds_in_an_hour) + $treshold;
            } catch (\Exception $e) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> Atttempting to schedule user synchronization but failed to parse time and recurrence values');
                return new \WP_Error('ScheduleParseError', __METHOD__ . ' -> Atttempting to schedule user synchronization but failed to parse time and recurrence values');
                return;
            }

            $job['next'] = $first_time;
            $job['last'] = null;
            $updated = self::update_user_sync_job($job);

            if (is_wp_error($updated)) {
                return $updated;
            }

            $result = wp_schedule_event($first_time, $recurrence, 'wpo_sync_v2_users_start', [$job_id]);

            if (is_wp_error($result)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> Could not schedule a new cron job to synchronize users for the first time at ' . $first_time . ' and then ' . $recurrence  . '[' . $result->get_error_message() . ']');
            }

            return true;
        }

        /**
         * Starts the user synchronization by calling the first collection / page of 
         * users from Office 365 and then recursively continues until finished. The
         * results are stored in custom WordPress table.
         * 
         * @since 15.0
         * 
         * @return mixed(bool|WP_Error) true if synchronization was successful otherwise WP_Error
         */
        public static function sync_users($job_id)
        {
            $job = self::get_user_sync_job_by_id($job_id);

            if (is_wp_error($job)) {
                return $job;
            }

            // Validate job
            $validated = self::user_sync_job_is_valid($job);

            if (is_wp_error($validated)) {
                Log_Service::write_log('ERROR', $validated->get_error_message());
                return $validated;
            }

            // Delete all scheduled jobs
            if ($job['trigger'] != 'schedule') {
                self::get_scheduled_events($job_id, true);
            }

            // Check if there is an instance of this job still running
            $job_info_name = sprintf('%s_wpo365_sync_next', $job_id);
            $job_info = Wpmu_Helpers::mu_get_transient($job_info_name);

            if (!empty($job_info)) {
                $message = sprintf(
                    '%s -> Could not start a new instance of user synchronization job with ID %s because another instance did not yet finish (please stop the job in progress before starting a new one)',
                    __METHOD__,
                    $job_id
                );
                Log_Service::write_log('ERROR', $message);
                return new \WP_Error('NotFinished', $message);
            }

            Log_Service::write_log('DEBUG', __METHOD__ . ' -> A new user synchronization job is starting and the old job data will be deleted');

            // Verify that the table has been created
            Sync_Db::user_sync_table_exists(true);

            // Delete previous log
            self::delete_job_data($job_id);

            // Generate new job.last
            $job['last'] = array(
                'id' => $job['id'] . '-' . uniqid(),
                'date' => time(),
                'error' => is_wp_error($validated) ? $validated->get_error_message() : null,
                'processed' => 0,
                'total' => -1,
            );

            $updated = self::update_user_sync_job($job);

            if (is_wp_error($updated)) {
                return $updated;
            }

            // Start
            return self::fetch_users($job_id, '/' . $job['query']);
        }

        /**
         * Fetches users from Microsoft Graph using the query supplied. Can be called recursively.
         *  
         * @since 15.0
         * 
         * @param   string  $graph_query    Query to call Microsoft Graph
         * @param   string  $job_id         ID of the job
         * @return  boolean|WP_Error        True if no errors occured otherwise an error will be returned
         */
        public static function fetch_users($job_id, $graph_query = null)
        {
            $job = self::get_user_sync_job_by_id($job_id);

            $stop = function ($error) use ($job, $job_id) {
                // Remove memoized job info
                $job_info_name = sprintf('%s_wpo365_sync_next', $job_id);
                Wpmu_Helpers::mu_delete_transient($job_info_name);

                // Send mail
                SyncV2_Service::sync_completed_notification($job_id);

                // Cancel any scheduled job
                SyncV2_Service::get_scheduled_events($job_id, true);

                if (!is_wp_error($job)) {
                    // Update job.last
                    $job['last']['error'] = $error->get_error_message();
                    $job['last']['stopped'] = true;
                    SyncV2_Service::update_user_sync_job($job);

                    // Trigger hook
                    do_action('wpo365/sync/error', $job);
                }
            };

            if (is_wp_error($job)) {
                $stop($job);
                return;
            }

            /**
             * @since   21.0    Job information is stored as a transient.
             */

            if (empty($graph_query)) {
                $job_info_name = sprintf('%s_wpo365_sync_next', $job_id);
                $graph_query = Wpmu_Helpers::mu_get_transient($job_info_name);

                if (empty($graph_query)) {
                    $stop(new \WP_Error('NextLinkExpired', sprintf(
                        '%s -> User sync job with ID %s cannot continue because the next-link has expired',
                        __METHOD__,
                        $job_id
                    )));
                    return;
                }
            }

            // Trigger hook
            do_action('wpo365/sync/before', $job);

            $fetch_result = self::process_fetch_result(Graph_Service::fetch($graph_query, 'GET', false, array('Accept: application/json;odata.metadata=minimal')), $job_id);

            if (is_wp_error($fetch_result)) {
                $stop($fetch_result);
            }
        }

        /**
         * Processes a collection of Office 365 users returned from the corresponding Microsoft Graph query. Recursively
         * calls for the next collection when finished processing with the current collection.
         * 
         * @since 15.0
         * 
         * @param stdClass  $response   Response returned by the MS Graph client that needs to be processed
         * @param string    $job_id     ID of the job
         * 
         * @return boolean|WP_Error
         */
        private static function process_fetch_result($response, $job_id)
        {
            $job = self::get_user_sync_job_by_id($job_id);

            if (is_wp_error($job)) {
                return $job;
            }

            $unscheduled_job_id = \get_option('wpo_sync_v2_users_unscheduled');

            // Check if administrator requested current job to be stopped
            if (false !== $unscheduled_job_id) {

                if (!empty($job['last']) && !empty($job['last']['id']) && $unscheduled_job_id == $job['last']['id']) {
                    delete_option('wpo_sync_v2_users_unscheduled');
                    return new \WP_Error('UserSyncStopped', __METHOD__ . ' -> Administrator requested user synchronization to stop');
                }
            }

            // Remember for the duration of this request that users are being synchronized
            $request_service = Request_Service::get_instance();
            $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
            $request->set_item('user_sync', true);

            if (!Graph_Service::is_fetch_result_ok($response, __METHOD__ . ' -> Could not fetch users for synchronization from Microsoft Graph')) {
                return new \WP_Error('GraphFetchError', __METHOD__ . ' -> Could not fetch users for synchronization from Microsoft Graph [check log]');
            }

            if (!is_array($response['payload']['value'])) {
                return new \WP_Error('GraphFetchError', __METHOD__ . ' -> Could not fetch users for synchronization from Microsoft Graph [no value returned]');
            }

            if (!empty($job['last'])) {
                $job['last']['processed'] = intval($job['last']['processed']) + sizeof($response['payload']['value']);
                $job['last']['date'] = time();

                if (Options_Service::get_global_boolean_var('use_b2c')) {
                    // For B2C the $count parameter is not supported at the moment 
                    $job['last']['total'] = ($job['last']['processed'] + 1);
                } elseif (!empty($response['payload']['@odata.count'])) {
                    $job['last']['total'] = $response['payload']['@odata.count'];
                }

                $updated = self::update_user_sync_job($job);

                if (is_wp_error($updated)) {
                    return $updated;
                }
            }

            foreach ($response['payload']['value'] as $o365_user) {
                // make sure the object is a user
                if (!empty($o365_user['@odata.type']) && WordPress_Helpers::stripos($o365_user['@odata.type'], 'user') === false) {
                    Log_Service::write_log('WARN', __METHOD__ . ' -> Not processing a directory object that is not a user');
                    continue;
                }

                // transform user to our own internal format
                $wpo_usr = User_Service::user_from_graph_user($o365_user);

                // Trigger hook
                do_action('wpo365/sync/user', $wpo_usr);

                // Azure AD user without upn cannot be processed
                if (!isset($wpo_usr->upn)) {
                    self::write_log($job['last']['id'], 'unknown', 'skipped', $wpo_usr, __METHOD__ . ' -> O365 user without userPrincipalName', -1);
                    Log_Service::write_log('WARN', __METHOD__ . ' -> O365 user without userPrincipalName');
                    continue;
                }

                // Azure AD guest user can only be processed if explicitely requested
                if ($job['membersOnly'] && false !== WordPress_Helpers::stripos($wpo_usr->upn, '#ext#')) {
                    self::write_log($job['last']['id'], 'unknown', 'skipped', $wpo_usr, __METHOD__ . ' -> User is not an internal user: ' . $wpo_usr->preferred_username, -1);
                    Log_Service::write_log('WARN', __METHOD__ . ' -> User is not an internal user: ' . $wpo_usr->preferred_username);
                    continue;
                }

                /**
                 * @since   21.0    Domain check has become optional.
                 */

                if (empty($job['skipDomainCheck'])) {
                    $domain = Domain_Helpers::get_smtp_domain_from_email_address($wpo_usr->upn);

                    if (empty($domain) || !Domain_Helpers::is_tenant_domain($domain)) {
                        self::write_log($job['last']['id'], 'unknown', 'skipped', $wpo_usr, __METHOD__ . ' -> User\'s UPN domain is not listed as custom domain: ' . $wpo_usr->preferred_username, -1);
                        Log_Service::write_log('WARN', __METHOD__ . ' -> User\'s UPN domain is not listed as custom domain: ' . $wpo_usr->preferred_username);
                        continue;
                    }
                }

                $wp_user = User_Service::try_get_user_by($wpo_usr);

                $user_created = false;
                $user_updated = false;

                // found a new Office 365 user
                if (empty($wp_user)) {

                    if ($job['actionCreateUser']) {

                        $wp_id = User_Create_Update_Service::create_user($wpo_usr, true, false);

                        if (empty($wp_id)) {
                            self::write_log($job['last']['id'], 'new_domain_user', 'error', $wpo_usr, __METHOD__ . ' -> Could not create WordPress user: ' . $wpo_usr->preferred_username, -1);
                            Log_Service::write_log('WARN', __METHOD__ . ' -> Could not create WordPress user: ' . $wpo_usr->preferred_username);
                            continue;
                        } else {
                            self::write_log($job['last']['id'], 'new_domain_user', 'created', $wpo_usr, __METHOD__ . ' -> Successfully created new WordPress user: ' . $wpo_usr->preferred_username, $wp_id);
                            $wp_user = \get_user_by('ID', $wp_id);
                            Log_Service::write_log('DEBUG', __METHOD__ . ' -> Successfully created new WordPress user: ' . $wpo_usr->preferred_username);
                            $user_created = true;
                        }
                    }
                }

                // update new and / or existing wp users with group and user info
                if ($user_created || (!empty($wp_user)  && $job['actionUpdateUser'])) {

                    // Save a user's principal name, tenant id and object id
                    update_user_meta($wp_user->ID, 'userPrincipalName', $wpo_usr->upn);
                    update_user_meta($wp_user->ID, 'aadTenantId', $wpo_usr->tid);
                    update_user_meta($wp_user->ID, 'aadObjectId', $wpo_usr->oid);

                    // When updating a user we want to make sure he / she is (no longer) deactivated
                    delete_user_meta($wp_user->ID, 'wpo365_active');

                    // Update role(s) assignment and extra user details
                    User_Create_Update_Service::update_user($wp_user->ID, $wpo_usr, true);

                    if (!$user_created) {
                        self::write_log($job['last']['id'], 'existing_domain_user', 'updated', $wpo_usr, __METHOD__ . ' -> Successfully updated existing WordPress user: ' . $wpo_usr->preferred_username, $wp_user->ID);
                        Log_Service::write_log('DEBUG', __METHOD__ . ' -> Successfully updated existing WordPress user: ' . $wpo_usr->preferred_username);
                    } else {
                        Log_Service::write_log('DEBUG', __METHOD__ . ' -> Successfully updated new WordPress user: ' . $wpo_usr->preferred_username);
                    }

                    $user_updated = true;
                }

                // tag wp user with sync job ID
                if (!empty($wp_user)) {
                    update_user_meta($wp_user->ID, 'wpo_sync_users_job_id', $job['last']['id']);
                    update_user_meta($wp_user->ID, 'wpo_sync_users_last_sync', $job['last']['date']);
                }

                // User not created / updated therefore logged instead
                if (!$user_created && !$user_updated) {
                    self::write_log($job['last']['id'], !empty($wp_user) ? 'existing_domain_user' : 'new_domain_user', 'logged', $wpo_usr, __METHOD__ . ' -> Successfully logged ' . (!empty($wp_user) ? 'existing' : 'new') . ' WordPress user: ' . $wpo_usr->preferred_username, (empty($wp_user) ? -1 : $wp_user->ID));
                    Log_Service::write_log('DEBUG', __METHOD__ . ' -> Successfully logged ' . (!empty($wp_user) ? 'existing' : 'new') . ' WordPress user: ' . $wpo_usr->preferred_username);
                }

                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Processed Azure AD user with principal user name ' . $wpo_usr->upn);
            }

            // continue with the next batch of users
            if (array_key_exists('@odata.nextLink', $response['payload'])) {
                $graph_version = Options_Service::get_global_string_var('graph_version');
                $graph_version = empty($graph_version) || $graph_version == 'current' ? 'v1.0' : $graph_version;
                $graph_url = 'https://graph.microsoft.com/' . $graph_version;
                $next_link = str_replace($graph_url, '', $response['payload']['@odata.nextLink']);
                $job_info_name = sprintf('%s_wpo365_sync_next', $job_id);
                Wpmu_Helpers::mu_set_transient($job_info_name, $next_link, 3600); // Next link remains valid for one hour
                $result = wp_schedule_single_event(time() - 60, 'wpo_sync_v2_users_next', [$job_id]);
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Next event for hook "wpo_sync_users_next" has been scheduled');
            } else {
                // Remove memoized job info
                $job_info_name = sprintf('%s_wpo365_sync_next', $job_id);
                Wpmu_Helpers::mu_delete_transient($job_info_name);

                $untagged_users_result = self::handle_untagged_users($job_id);

                if (is_wp_error($untagged_users_result)) {
                    return $untagged_users_result;
                }

                $next_cron_jobs = self::get_scheduled_events($job_id);

                if (!empty($next_cron_jobs)) {

                    foreach ($next_cron_jobs as $cron_timestamp => $cron_job) {

                        if ($cron_job['hook'] == 'wpo_sync_v2_users_start') {
                            $job['next'] = $cron_timestamp;
                            self::update_user_sync_job($job);
                        }
                    }
                }

                self::sync_completed_notification($job_id);

                // Mark job as stopped
                $job['last']['stopped'] = true;
                $job['last']['date'] = time();
                $job['last']['total'] = $job['last']['processed'];
                self::update_user_sync_job($job);

                // Trigger hook
                do_action('wpo365/sync/after', $job);
            }

            return true;
        }

        /**
         * Stops user synchronization.
         * 
         * @since   15.3
         * 
         * @param   string  $job_id     The ID of the job to stop
         * @return  boolean|WP_Error    
         */
        public static function stop($job_id)
        {
            $job = self::get_user_sync_job_by_id($job_id);

            if (is_wp_error($job)) {
                return $job;
            }

            // Inform the user synchronization processor to stop
            if (!empty($job['last'])) {
                $job['last']['error'] = __METHOD__ . ' -> Administrator requested user synchronization to stop';
                $job['last']['stopped'] = true;
                $job['last']['date'] = time();
                \update_option('wpo_sync_v2_users_unscheduled', $job['last']['id']);
            }

            // Delete the schedule configuration
            $job['schedule'] = null;
            $job['next'] = null;

            // Update
            self::update_user_sync_job($job);

            // Delete all scheduled jobs
            self::get_scheduled_events($job_id, true);

            // Delete memoized job info
            $job_info_name = sprintf('%s_wpo365_sync_next', $job_id);
            Wpmu_Helpers::mu_delete_transient($job_info_name);

            // Send email
            self::sync_completed_notification($job_id);

            // Trigger hook
            do_action('wpo365/sync/error', $job);

            return true;
        }

        /**
         * @since 10.0
         * 
         * @param $delete bool Whether or not the scheduled events should be deleted
         * @return array Collection of scheduled events
         */
        public static function get_scheduled_events($job_id, $delete = false)
        {
            $cron_jobs = _get_cron_array();
            $wpo_sync_jobs = array();

            foreach ($cron_jobs as $timestamp => $array_of_jobs) {

                foreach ($array_of_jobs as $hook => $jobs) {

                    if ($hook == 'wpo_sync_users_start' || $hook == 'wpo_sync_users' || $hook == 'wpo_sync_v2_users_start' || $hook == 'wpo_sync_v2_users_next') {

                        foreach ($jobs as $id => $job) {
                            $job['hook'] = $hook; // Add the hook back so it can be used.
                            $wpo_sync_jobs[$timestamp] = $job;

                            if ($delete) {

                                if (empty($job_id) || $job['args'][0] == $job_id) {
                                    $nr_of_unscheduled_events = wp_clear_scheduled_hook($hook, $job['args']);
                                    Log_Service::write_log('DEBUG', __METHOD__ . ' -> Unscheduled ' . $nr_of_unscheduled_events . ' cron jobs [hook: ' . $hook . ']');
                                }
                            }
                        }
                    }
                }
            }

            return $wpo_sync_jobs;
        }

        /**
         * Gets a summary of the user synchronization results (all, by status, by record type).
         * 
         * @since 15.0
         * 
         * @param   string  $sync_job_id    The ID of the job instance.
         * @return  array   Array representation of the summarized results (all, by status, by record type).
         */
        public static function get_results_summary($job_id, $keyword = null, $status = null)
        {
            $job = self::get_user_sync_job_by_id($job_id);

            if (is_wp_error($job)) {
                return array(
                    'all'               => 0,
                    'created'           => 0,
                    'deleted'           => 0,
                    'deactivated'       => 0,
                    'updated'           => 0,
                    'error'             => 0,
                    'logged'            => 0,
                    'skipped'           => 0,
                    'info'              => $job->get_error_message(),
                );
            }

            $job_id_last = $job['last']['id'];

            global $wpdb;

            $table_name = Sync_Db::get_user_sync_table_name();
            $keyword_clause = !empty($keyword) ? " AND upn LIKE '%$keyword%' " : '';
            $status_clause = !empty($status) ? " AND sync_job_status = '$status' " : '';

            return array(
                'all'               => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' "),
                'created'           => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' $keyword_clause $status_clause AND sync_job_status = 'created'"),
                'deleted'           => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' $keyword_clause $status_clause AND sync_job_status = 'deleted'"),
                'deactivated'       => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' $keyword_clause $status_clause AND sync_job_status = 'deactivated'"),
                'updated'           => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' $keyword_clause $status_clause AND sync_job_status = 'updated'"),
                'error'             => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' $keyword_clause $status_clause AND sync_job_status = 'error'"),
                'logged'            => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' $keyword_clause $status_clause AND sync_job_status = 'logged'"),
                'skipped'           => $wpdb->get_var("SELECT COUNT(upn) FROM $table_name WHERE sync_job_id = '$job_id_last' $keyword_clause $status_clause AND sync_job_status = 'skipped'"),
                'info'              => $job['last']['error'],
            );
        }

        /**
         * Gets a page of the paged results of the user synchronization results.
         * 
         * @since 15.0
         * 
         * @param   string  $sync_job_id    The ID of the job instance.
         * @param   int     $page_size      Number of results to retrieve
         * @param   int     $offset         Number of results to skip before retrieving a page of results
         * 
         * @return  array   Array representation of results.
         */
        public static function get_results($job_last_id, $page_size, $offset, $keyword = null, $status = null)
        {

            global $wpdb;

            $table_name = Sync_Db::get_user_sync_table_name();
            $keyword_clause = !empty($keyword) ? " AND upn LIKE '%$keyword%' " : '';
            $status_clause = !empty($status) ? " AND sync_job_status = '$status' " : '';

            return $wpdb->get_results("SELECT * FROM $table_name WHERE sync_job_id = '$job_last_id' $keyword_clause $status_clause LIMIT $page_size OFFSET $offset", ARRAY_A);
        }

        /**
         * Will get user sync job by job.last.id.
         * 
         * @since 15.0
         * 
         * @param   string  $job_last_id    The ID of the job.
         * @return  array|WP_Error          The job found or an error if not found.
         */
        public static function get_user_sync_job_by_last_id($job_last_id)
        {
            $jobs = Options_Service::get_global_list_var('user_sync_jobs', false);
            $job = 0;

            foreach ($jobs as $_job) {

                if (!empty($_job['last']) && $_job['last']['id'] == $job_last_id) {
                    $job = $_job;
                    break;
                }
            }

            if (empty($job)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> User synchronization stopped [Job with job.last.ID ' . $job_last_id . ' not found.]');
                return new \WP_Error('NotFound', __METHOD__ . ' -> Job with job.last.ID ' . $job_last_id . ' not found.');
            }

            return $job;
        }

        /**
         * Helper method to truncate the table and remove the job id and last run time.
         * 
         * @since 15.0
         * 
         * @return void
         */
        public static function delete_job_data($job_id)
        {

            global $wpdb;

            $table_name = Sync_Db::get_user_sync_table_name();
            $wpdb->query("DELETE FROM $table_name WHERE sync_job_id LIKE '$job_id-%';");
        }

        /**
         * 
         * @since   15.0
         * 
         * @param   string  $sync_job_id        ID of the job instance
         * @param   string  $record_type        One of the following options:
         *                                      unknown                 -> Type of user indeterminate
         *                                      new_domain_user         -> Azure AD user without WordPress account
         *                                      existing_domain_user    -> Azure AD user with a WordPress account
         * @param   string  $action_performed   One of the following options:
         *                                      created                 -> New WordPress user created
         *                                      deleted                 -> WordPress user deleted
         *                                      error                   -> WordPress user could not be created or updated
         *                                      skipped                 -> Azure AD user has not been processed
         *                                      updated                 -> Existing WordPress user updated
         * @param   User    $wpo_usr
         * @param   string  $notes
         * @param   int     $wp_user_id          
         */
        private static function write_log($sync_job_last_id, $record_type, $action_performed, $wpo_usr, $notes = '', $wp_user_id = -1)
        {
            global $wpdb;

            $table_name = Sync_Db::get_user_sync_table_name();

            if (intval($wpdb->get_var("SELECT COUNT(*) as num_rows FROM " . $table_name . " WHERE upn = '" .  $wpo_usr->upn . "' AND sync_job_id = '" . $sync_job_last_id . "'")) === 0) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'wp_id'             => $wp_user_id,
                        'upn'               => $wpo_usr->upn,
                        'first_name'        => $wpo_usr->first_name,
                        'last_name'         => $wpo_usr->last_name,
                        'full_name'         => $wpo_usr->full_name,
                        'email'             => $wpo_usr->email,
                        'sync_job_id'       => $sync_job_last_id,
                        'name'              => $wpo_usr->name,
                        'sync_job_status'   => $action_performed,
                        'record_type'       => $record_type,
                        'notes'             => $notes,
                    )
                );
            } else {
                $message = sprintf(
                    '%s -> Trying to create a duplicate log entry for %s [%s]',
                    __METHOD__,
                    $wpo_usr->preferred_username,
                    $sync_job_last_id
                );
                Log_Service::write_log('ERROR', $message);
            }
        }

        /**
         * Will try and retrieve a user sync job by ID from the WPO365 configuration.
         * 
         * @since 15.0
         * 
         * @param   string  $job_id     The ID of the job.
         * @return  array|WP_Error      The job found or an error if not found.
         */
        private static function get_user_sync_job_by_id($job_id)
        {
            $jobs = Options_Service::get_global_list_var('user_sync_jobs', false);
            $job = 0;

            foreach ($jobs as $_job) {

                if ($_job['id'] == $job_id) {
                    $job = $_job;
                    break;
                }
            }

            if (empty($job)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> User synchronization stopped [Job with ID ' . $job_id . ' not found.]');
                return new \WP_Error('NotFound', __METHOD__ . ' -> Job with ID ' . $job_id . ' not found.');
            }

            // Ensure the $count parameter to be able to keep track on progress.
            $job['query'] = SyncV2_Service::add_query_count_param($job['query']);

            return $job;
        }

        /**
         * Checks if query contains the $count(=true) parameter and if not adds it.
         * 
         * @since 15.0
         * 
         * @param   string  Query
         * @return  string  Updated query with $count=true param.
         * 
         */
        private static function add_query_count_param($query)
        {

            if (WordPress_Helpers::stripos($query, '$count=') === false) {
                return WordPress_Helpers::stripos($query, '?') !== false
                    ? $query . '&$count=true'
                    : $query . '?$count=true';
            }

            return $query;
        }

        /**
         * Validates the user sync job.
         * 
         * @since 15.0
         * 
         * @param   $job    array   The user sync job to be validated.
         * @return  array|WP_Error  The job or WP_Error if invalid.
         */
        private static function user_sync_job_is_valid($job)
        {
            $error_fields = array();

            if (empty($job['name'])) {
                $error_fields[] = 'name is empty';
            }

            if (empty($job['query'])) {
                $error_fields[] = 'query is empty';
            }

            if (empty(filter_var($job['queryTested'], FILTER_VALIDATE_BOOLEAN) === false)) {
                $error_fields[] = 'query is not tested';
            }

            if ($job['trigger'] == 'schedule' && empty($job['schedule'])) {
                $error_fields[] = 'job schedule is empty';
            }

            if ($job['sendLog'] && empty($job['sendLogTo'])) {
                $error_fields[] = 'mail recipient to send log is empty';
            }

            if (filter_var($job['actionDeleteUser'], FILTER_VALIDATE_BOOLEAN) === true && empty($job['reassignPostsToId'])) {
                $error_fields[] = 'user to re-assign posts to is empty';
            }

            if (!empty($errors)) {
                $last_error = __METHOD__ . ' -> User synchronization stopped [User sync job is invalid: ' . join(', ', $error_fields) . ']';
                Log_Service::write_log('ERROR', $last_error);
                return new \WP_Error('ArgumentException', $last_error);
            }

            return $job;
        }

        /**
         * Updates the job provided in the array of jobs that are stored 
         * as part of the WPO365 settings.
         * 
         * @since 15.0
         * 
         * @param   array   $job        The job to be updated in the array
         * @return  boolean|WP_Error    True if no error occurred otherwise WP_Error
         */
        private static function update_user_sync_job($job)
        {
            $jobs = Options_Service::get_global_list_var('user_sync_jobs', false);
            $job_index = -1;

            foreach ($jobs as $i => $_job) {

                if ($_job['id'] == $job['id']) {
                    $job_index = $i;
                    break;
                }
            }

            if ($job_index == -1) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> User synchronization stopped [Job with ID ' . $job['id'] . ' not found.]');
                return new \WP_Error('NotFoundException', __METHOD__ . ' -> User synchronization stopped [Job with ID ' . $job['id'] . ' not found.]');
            }

            $jobs[$job_index] = $job;
            Options_Service::add_update_option('user_sync_jobs', $jobs);

            return true;
        }

        /**
         * Queries all users for the current job tag and if not found will add those users
         * to the user sync table as untagged users (no matching Office 365 user was found).
         * 
         * @since 15.0
         * 
         * @return bool|WP_Error
         */
        private static function handle_untagged_users($job_id)
        {

            $job = self::get_user_sync_job_by_id($job_id);

            if (is_wp_error($job)) {
                return $job;
            }

            $untagged_users_query = new \WP_User_Query(
                array(
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key'       => 'wpo_sync_users_job_id',
                            'value'     => $job['last']['id'],
                            'compare'   => '!=',
                        ),
                        array(
                            'key'       => 'wpo_sync_users_job_id',
                            'compare'   => 'NOT EXISTS',
                        )
                    )
                )
            );

            require_once(ABSPATH . 'wp-admin/includes/user.php');

            $table_name = Sync_Db::get_user_sync_table_name();
            $soft_delete = !empty($job['softDeleteUsers']);
            $reassign_to = !empty($job['reassignPostsToId']) ? intval($job['reassignPostsToId']) : null;

            // And fill it with the results of the last run
            $untagged_users = $untagged_users_query->get_results();

            foreach ($untagged_users as $untagged_user) {
                $skip_delete = false;
                $sync_job_status = 'skipped';
                $record_type = 'wordpress_user';

                $wp_user = get_user_by('ID', $untagged_user->ID);
                $notes = '';

                if (is_wp_error($wp_user)) {
                    Log_Service::write_log('WARN', __METHOD__ . ' -> Cannot retrieve untagged user with login ' . $wp_user->user_login . ' because user cannot be found');
                    continue;
                }

                if (!Options_Service::get_global_boolean_var('update_admins') && Permissions_Helpers::user_is_admin($wp_user)) {
                    $notes = __METHOD__ . ' -> Not deleting user with login ' . $wp_user->user_login . ' because user has administrator capabilities';
                    Log_Service::write_log('WARN', $notes);
                    $skip_delete = true;
                }

                /**
                 * @since   21.0    Domain check has become optional.
                 */

                if (empty($job['skipDomainCheck'])) {
                    $domain = Domain_Helpers::get_smtp_domain_from_email_address($wp_user->user_login);

                    if (empty($domain) || !Domain_Helpers::is_tenant_domain($domain)) {
                        $domain = Domain_Helpers::get_smtp_domain_from_email_address($wp_user->user_email);
                    }

                    if (empty($domain) || !Domain_Helpers::is_tenant_domain($domain)) {
                        $notes = __METHOD__ . ' -> Cannot delete user with ID ' . $wp_user->user_login . ' because this user is not an Azure AD user';
                        Log_Service::write_log('WARN', $notes);
                        $skip_delete = true;
                    }
                } else {
                    $aad_object_id = get_user_meta($wp_user->ID, 'aadObjectId', true);

                    if (empty($aad_object_id)) {
                        $notes = __METHOD__ . ' -> Cannot delete user with ID ' . $wp_user->user_login . ' because this user is not maintained by the WPO365 plugin';
                        Log_Service::write_log('DEBUG', $notes);
                        $skip_delete = true;
                    }
                }

                // Finally commit deletion of WP users if requested
                if (!$skip_delete && true === $job['actionDeleteUser']) {

                    if (!$soft_delete) {
                        $sync_job_status = wp_delete_user($wp_user->ID, $reassign_to) ? 'deleted' : 'error';
                    } else {
                        \update_user_meta($wp_user->ID, 'wpo365_active', 'deactivated');
                        $sync_job_status = 'deactivated';

                        // Remove all roles of the deactivated user
                        foreach ($wp_user->roles as $current_user_role) {
                            $wp_user->remove_role($current_user_role);
                        }
                    }
                }

                global $wpdb;

                if (intval($wpdb->get_var("SELECT COUNT(*) as num_rows FROM $table_name WHERE upn = '" .  $wp_user->user_login . "' AND sync_job_id = '" . $job['last']['id'] . "'")) === 0) {
                    $res = $wpdb->insert(
                        $table_name,
                        array(
                            'wp_id'             => $wp_user->ID,
                            'upn'               => $wp_user->user_login,
                            'first_name'        => '', // defined in user meta
                            'last_name'         => '', // defined in user meta
                            'full_name'         => isset($wp_user->display_name) ? $wp_user->display_name : '',
                            'email'             => isset($wp_user->user_email) ? $wp_user->user_email : '',
                            'sync_job_id'       => $job['last']['id'],
                            'name'              => $wp_user->user_login,
                            'sync_job_status'   => $sync_job_status,
                            'record_type'       => $record_type,
                            'notes'             => $notes,
                        )
                    );
                } else {
                    Log_Service::write_log('ERROR', __METHOD__ . ' -> Trying to create a duplicate log entry for ' . $wp_user->user_login);
                }
            }

            return true;
        }

        /**
         * Helper to register custom columns to show a couple of WPO365 User synchronization related fields 
         * on the default WordPress Users screen.
         * 
         * @since   21.0
         * 
         * @param   Array   Array of columns
         * 
         * @return  Arry    Array of colums with a couple of Azure AD related columns added.
         */
        public static function register_users_sync_columns($columns)
        {
            $columns['wpo365_synchronized'] = __('Last sync', 'wpo365-login');
            $columns['wpo365_deactivated'] = __('De-activated', 'wpo365-login');;
            return $columns;
        }

        /**
         * Helper to render a couple of custom WPO365 User sync columns that are added to the default WordPress Users screen.
         * 
         * @since   21.0
         * 
         * @param   string  $output         Rendered HTML
         * @param   string  $column_name    Name of the column being rendered
         * @param   string  $user_id        ID of the user the column's cell is being rendered for
         * 
         * @return  string  Rendered HTML.
         */
        public static function render_users_sync_columns($output, $column_name, $user_id)
        {
            if ('wpo365_synchronized' == $column_name) {
                $last_sync = get_user_meta($user_id, 'wpo_sync_users_last_sync', true);

                if (empty($last_sync)) {
                    return $output;
                }

                $formatted = date('Y-m-d H:i', $last_sync);

                return sprintf('<div><span>%s</span></div>', $formatted);
            }

            if ('wpo365_deactivated' == $column_name) {
                $deactivated = get_user_meta($user_id, 'wpo365_active', true);

                if ($deactivated == 'deactivated') {
                    $url = add_query_arg('wpo365_reactivate_user', $user_id);
                    return sprintf('<div><span><button type="button" onclick="window.location.href = \'%s\'">Reactivate</button></span></div>', $url);
                }
            }

            return $output;
        }

        /**
         * Helper to reactivate a user from the WP users list.
         * 
         * @since 21.0
         * 
         * @return void
         */
        public static function reactivate_user()
        {
            if (isset($_GET['wpo365_reactivate_user'])) {
                $wp_user_id = (int) sanitize_text_field($_GET['wpo365_reactivate_user']);
                $wp_user = get_user_by('ID', $wp_user_id);

                if (!is_wp_error($wp_user)) {
                    delete_user_meta($wp_user->ID, 'wpo365_active');
                }

                Url_Helpers::force_redirect(remove_query_arg('wpo365_reactivate_user'));
            }
        }

        /**
         * Sends the admin of the site an email to inform that user synchronization has completed.
         * 
         * @since 15.0
         * 
         * @return void
         */
        private static function sync_completed_notification($job_id)
        {

            $job = self::get_user_sync_job_by_id($job_id);

            if (is_wp_error($job)) {
                Log_Service::write_log('WARN', __METHOD__ . ' -> Could not find the user synchronization job whilst trying to send user-synchronization-completed email');
                return;
            }

            if (empty($job['sendLog']) || empty($job['sendLogTo'])) {
                Log_Service::write_log('WARN', __METHOD__ . ' -> Sending of a user-synchronization-completed email is not configured');
                return;
            }

            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
            $summary = self::get_results_summary($job_id);

            include(Extensions_Helpers::get_active_extension_dir(array('wpo365-login-premium/wpo365-login.php', 'wpo365-login-intranet/wpo365-login.php')) . '/templates/sync-mail-text.php');

            $message = empty($summary['info'])
                ? sprintf($body, $blogname, $job['name'], $summary['all'], $summary['created'], $summary['deleted'], $summary['deactivated'], $summary['updated'], $summary['error'], $summary['logged'], $summary['skipped'])
                : sprintf($body, $blogname, $job['name'], $summary['info']);

            $subject = empty($summary['info'])
                ? sprintf('WPO365 | User Synchronization SUCCEEDED on your site [%s]', $blogname)
                : sprintf('WPO365 | User Synchronization FAILED on your site [%s]', $blogname);

            $sync_completed_email_admin = array(
                'to'      => $job['sendLogTo'],
                'subject' => $subject,
                'message' => $message,
                'headers' => array('Content-Type: text/plain'),
            );

            @wp_mail(
                $sync_completed_email_admin['to'],
                $sync_completed_email_admin['subject'],
                $sync_completed_email_admin['message'],
                $sync_completed_email_admin['headers']
            );
        }
    }
}
