<?php

namespace Wpo\Services;

// Prevent public access to this script
defined('ABSPATH') or die();

use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

if (!class_exists('\Wpo\Services\User_Create_Update_Service')) {

    class User_Create_Update_Service
    {

        /**
         * Updates a WordPress user.
         * 
         * @since   11.0
         * 
         * @param   mixed   $wp_user_id     WordPress ID of the user that will be updated
         * @param   bool    $is_deamon      If true then actions that may sign out the user are ignored
         * @param   bool    $exit_on_error  If true the user my be signed out if an action fails
         * 
         * @return  int     The WordPress ID of the user.
         */
        public static function create_user(&$wpo_usr, $is_deamon = false, $exit_on_error = true)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $user_login = !empty($wpo_usr->preferred_username)
                ? $wpo_usr->preferred_username
                : $wpo_usr->upn;

            /**
             * @since 12.5 
             * 
             * Don't create a user when that user should not be added to a subsite in case of wpmu shared mode.
             */
            if (!$is_deamon && is_multisite() && !Options_Service::mu_use_subsite_options() && !is_main_site() && Options_Service::get_global_boolean_var('skip_add_user_to_subsite')) {
                $blog_id = get_current_blog_id();

                // Not using subsite options and administrator has disabled automatic adding of users to subsites
                Log_Service::write_log('WARN', __METHOD__ . " -> Skipped creating a user with login $user_login for blog with ID $blog_id because administrator has disabled adding a user to a subsite");
                Authentication_Service::goodbye(Error_Service::USER_NOT_FOUND);
                exit();
            }

            if (!$is_deamon && !Options_Service::get_global_boolean_var('create_and_add_users')) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> User not found and settings prevented creating a new user on-demand for user ' . $user_login);
                Authentication_Service::goodbye(Error_Service::USER_NOT_FOUND);
                exit();
            }

            if (Options_Service::get_global_boolean_var('use_short_login_name')) {
                $user_login = \stristr($user_login, '@', true);
            }

            $usr_default_role = is_main_site()
                ? Options_Service::get_global_string_var('new_usr_default_role')
                : Options_Service::get_global_string_var('mu_new_usr_default_role');

            $password_length = Options_Service::get_global_numeric_var('password_length');

            if (empty($password_length) || $password_length < 16) {
                $password_length = 16;
            }

            $userdata = array(
                'user_login'    => $user_login,
                'user_pass'     => wp_generate_password($password_length, true, false),
                'display_name'  => $wpo_usr->full_name,
                'user_email'    => $wpo_usr->email,
                'first_name'    => $wpo_usr->first_name,
                'last_name'     => $wpo_usr->last_name,
                'role'          => $usr_default_role,
            );

            /**
             * @since 9.4 
             * 
             * Optionally removing any user_register hooks as these more often than
             * not interfer and cause unexpected behavior.
             */

            $user_regiser_hooks = null;

            if (Options_Service::get_global_boolean_var('skip_user_register_action') && isset($GLOBALS['wp_filter']) && isset($GLOBALS['wp_filter']['user_register'])) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Temporarily removing all filters for the user_register action to avoid interference');
                $user_regiser_hooks = $GLOBALS['wp_filter']['user_register'];
                unset($GLOBALS['wp_filter']['user_register']);
            }

            // Insert in Wordpress DB
            $wp_usr_id = wp_insert_user($userdata);

            if (!empty($GLOBALS['wp_filter']) && !empty($user_regiser_hooks)) {
                $GLOBALS['wp_filter']['user_register'] = $user_regiser_hooks;
            }

            if (is_wp_error($wp_usr_id)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> Could not create wp user. See next line for error information.');
                Log_Service::write_log('ERROR', $wp_usr_id);

                if ($exit_on_error) {
                    Authentication_Service::goodbye(Error_Service::CHECK_LOG);
                    exit();
                }

                return 0;
            }

            /**
             * @since 15.0
             */

            do_action('wpo365/user/created', $wp_usr_id);

            $wpo_usr->created = true;
            Log_Service::write_log('DEBUG', __METHOD__ . ' -> Created new user with ID ' . $wp_usr_id);

            // WPMU -> Add user to current blog
            if (\class_exists('\Wpo\Services\User_Create_Service') && \method_exists('\Wpo\Services\User_Create_Service', 'wpmu_add_user_to_blog')) {
                \Wpo\Services\User_Create_Service::wpmu_add_user_to_blog($wp_usr_id, $wpo_usr->preferred_username);
            }

            if (\class_exists('\Wpo\Services\User_Role_Service') && \method_exists('\Wpo\Services\User_Role_Service', 'update_user_roles')) {
                \Wpo\Services\User_Role_Service::update_user_roles($wp_usr_id, $wpo_usr);
            }

            // Try and send new user email
            if (\class_exists('\Wpo\Services\Mail_Notifications_Service')) {

                if (Options_Service::get_global_boolean_var('new_usr_send_mail')) {
                    $notify = Options_Service::get_global_boolean_var('new_usr_send_mail_admin_only')
                        ? 'admin'
                        : 'both';
                    \Wpo\Services\Mail_Notifications_Service::new_user_notification($wp_usr_id, null, $notify);
                    Log_Service::write_log('DEBUG', __METHOD__ . ' -> Sent new user notification');
                }
            } else {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Did not sent new user notification');
            }

            return $wp_usr_id;
        }

        /**
         * Updates a WordPress user.
         * 
         * @param   mixed   $wp_user_id     WordPress ID of the user that will be updated
         * @param   mixed   $wpo_usr        Internal user representation (Graph and ID token data)
         * @param   bool    $is_deamon      If true then actions that may sign out the user are ignored
         * 
         * @return  void
         */
        public static function update_user($wp_usr_id, $wpo_usr, $is_deamon = false)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Save user's UPN
            if (!empty($wpo_usr->upn)) {
                update_user_meta($wp_usr_id, 'userPrincipalName', $wpo_usr->upn);
            }

            if (!$wpo_usr->created) {

                if (!$is_deamon && \class_exists('\Wpo\Services\User_Create_Service') && \method_exists('\Wpo\Services\User_Create_Service', 'wpmu_add_user_to_blog')) {
                    \Wpo\Services\User_Create_Service::wpmu_add_user_to_blog($wp_usr_id, $wpo_usr->preferred_username);
                }

                if (\class_exists('\Wpo\Services\User_Role_Service') && \method_exists('\Wpo\Services\User_Role_Service', 'update_user_roles')) {
                    \Wpo\Services\User_Role_Service::update_user_roles($wp_usr_id, $wpo_usr);
                }
            }

            // Update Avatar
            if (Options_Service::get_global_boolean_var('use_avatar') && class_exists('\Wpo\Services\Avatar_Service')) {
                $default_avatar = get_avatar($wp_usr_id);
            }

            // Update custom fields
            if (class_exists('\Wpo\Services\User_Custom_Fields_Service')) {

                if (Options_Service::get_global_boolean_var('use_saml') && Options_Service::get_global_string_var('extra_user_fields_source') == 'samlResponse') {
                    \Wpo\Services\User_Custom_Fields_Service::update_custom_fields_from_saml_attributes($wp_usr_id, $wpo_usr);
                } else {
                    \Wpo\Services\User_Custom_Fields_Service::update_custom_fields($wp_usr_id, $wpo_usr);
                }
            }

            // Update default WordPress user fields
            self::update_wp_user($wp_usr_id, $wpo_usr);
        }

        /**
         * @since 11.0
         */
        private static function update_wp_user($wp_usr_id, $wpo_usr)
        {

            // Update "core" WP_User fields
            $wp_user_data = array('ID' => $wp_usr_id);

            if (!empty($wpo_usr->email)) {
                $wp_user_data['user_email'] = $wpo_usr->email;
            }

            if (!empty($wpo_usr->first_name)) {
                $wp_user_data['first_name'] = $wpo_usr->first_name;
            }

            if (!empty($wpo_usr->last_name)) {
                $wp_user_data['last_name'] = $wpo_usr->last_name;
            }

            if (!empty($wpo_usr->full_name)) {
                $wp_user_data['display_name'] = $wpo_usr->full_name;
            }

            wp_update_user($wp_user_data);
        }
    }
}
