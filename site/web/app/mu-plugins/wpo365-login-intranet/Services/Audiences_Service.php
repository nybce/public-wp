<?php

namespace Wpo\Services;

use WP_Post;
use \Wpo\Core\WordPress_Helpers;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Services\Audiences_Service')) {

    class Audiences_Service
    {

        /**
         * Registers the wpo365_audiences post_meta for each (custom) post type.
         * 
         * @since   16.0
         * 
         * @return  void
         */
        public static function aud_register_post_meta()
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $post_types = get_post_types();
            $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');

            foreach ($post_types as $post_type) {

                if (\in_array($post_type, $excluded_post_types)) {
                    continue;
                }

                register_post_meta($post_type, 'wpo365_audiences', array(
                    'single' => false,
                    'type' => 'string',
                    'show_in_rest' => true,
                    'auth_callback' => function () {
                        return current_user_can('delete_posts');
                    }
                ));

                register_post_meta($post_type, 'wpo365_private', array(
                    'single' => true,
                    'type' => 'boolean',
                    'show_in_rest' => true,
                    'auth_callback' => function () {
                        return current_user_can('delete_posts');
                    }
                ));
            }
        }

        /**
         * Registers a hook observer for each (supported) post / page type that will delete
         * the audience post_meta if an Audience block is not found.
         * 
         * @since   16.0
         * 
         * @return  void
         */
        public static function post_updated($post_id, $post_after, $post_before)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            if (!has_block('wpo365/aud', $post_id)) {
                $audiences = \get_post_meta($post_id, 'wpo365_audiences', false);

                if (!empty($audiences)) {
                    delete_post_meta($post_id, 'wpo365_audiences');
                    Log_Service::write_log('DEBUG', __METHOD__ . ' -> Delete wpo365_audiences post_meta for a post without corresponding WPO365 Audiences block');
                }

                $is_private = \get_post_meta($post_id, 'wpo365_private', true);

                if (!empty($is_private)) {
                    delete_post_meta($post_id, 'wpo365_private');
                    Log_Service::write_log('DEBUG', __METHOD__ . ' -> Delete wpo365_private post_meta for a post without corresponding WPO365 Audiences block');
                }
            }
        }

        /**
         * Updates the user's audience assignments (if any) based on the user's
         * Azure AD group membership(s) and the configured audiences.
         * 
         * @since   16.0
         * 
         * @param   string  $wp_usr_id  The ID of the user.
         * @param   User    $wpo_usr    The internal (Microsft Graph) based user representation.
         * 
         * @return  void 
         */
        public static function aad_group_x_audience($wp_usr_id, $wpo_usr)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Do nothing if audiences has not been enabled
            if (empty(Options_Service::get_global_boolean_var('enable_audiences'))) {
                return;
            }

            if (sizeof($wpo_usr->groups) == 0) {
                return;
            }

            $audiences = Options_Service::get_global_list_var('audiences');
            $user_audiences = array();

            foreach ($audiences as $audience) {

                // Just to be sure that the configuration object valid
                if (empty($audience['values']) || empty($audience['key'])) {
                    continue;
                }

                // Iterate over all the Azure AD group IDs added to this audience
                foreach ($audience['values'] as $value) {

                    // User is member of a group that is mapped to an audience
                    if (false === \in_array($value, $user_audiences) && true === \array_key_exists($value, $wpo_usr->groups)) {
                        $user_audiences[] = $audience['key'];
                    }
                }
            }

            update_user_meta($wp_usr_id, 'wpo365_audiences', $user_audiences);
        }

        /**
         * Hooked into 
         * 
         * @since   16.0
         * 
         * @see     ...
         */
        public static function posts_where($where, $query)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Do nothing if audiences has not been enabled

            if (empty(Options_Service::get_global_boolean_var('enable_audiences', false))) {
                return $where;
            }

            $wp_usr = \wp_get_current_user();

            // Check if we need to skip audiences for the current user's role

            if (self::skip_audiences($wp_usr)) {
                return $where;
            }

            global $wpdb;
            $andor = 'AND';

            // Handle excluded post types

            $db_posts = $wpdb->prefix . "posts";
            $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');

            if (!empty($excluded_post_types)) {
                $post_type_clause = '(';
                $post_type_andor = '';

                foreach ($excluded_post_types as $excluded_post_type) {
                    $post_type_clause .= " $post_type_andor post_type = '$excluded_post_type'";
                    $post_type_andor = 'OR';
                }

                $post_type_clause .= ')';

                $where .= " AND ( $db_posts.ID IN (SELECT ID FROM $db_posts WHERE $post_type_clause) ";
                $andor = 'OR';
            }

            // Handle audiences

            $db_postmeta = $wpdb->prefix . "postmeta";

            $user_audiences = \get_user_meta($wp_usr->ID, 'wpo365_audiences', true);

            if (empty($user_audiences)) {

                // User not in any audience -> Allow the user to view all posts without audiences.

                $where .= " $andor $db_posts.ID NOT IN (SELECT post_id FROM $db_postmeta WHERE meta_key = 'wpo365_audiences' AND meta_value IS NOT NULL AND meta_value != '') ";
            } else {
                $meta_value_clause = ' AND (';
                $meta_value_andor = '';

                foreach ($user_audiences as $audience) {
                    $meta_value_clause .= " $meta_value_andor meta_value = '$audience'";
                    $meta_value_andor = 'OR';
                }

                $meta_value_clause .= ')';

                // User in some audiences -> Allow the user to view all posts without audiences plus the ones where his / her audience has been added.

                $where .= " $andor ($db_posts.ID NOT IN (SELECT post_id FROM $db_postmeta WHERE meta_key = 'wpo365_audiences' AND meta_value IS NOT NULL AND meta_value != '') OR ID IN (SELECT post_id FROM $db_postmeta WHERE meta_key = 'wpo365_audiences' $meta_value_clause)) ";
            }

            // Do not show pages that are marked private

            if ($wp_usr->ID === 0) {

                $auth_scenario = Options_Service::get_global_string_var('auth_scenario');

                if ($auth_scenario == 'internet' || $auth_scenario == 'internetAuthOnly') {
                    $where .= " $andor ($db_posts.ID NOT IN (SELECT post_id FROM $db_postmeta WHERE meta_key = 'wpo365_private' AND meta_value IS NOT NULL AND meta_value = 1)) ";
                }
            }

            if ($andor == 'OR') {
                $where .= ')';
            }

            return $where;
        }

        /**
         * Hooked into 
         * 
         * @since   16.0
         * 
         * @see     ...
         */
        public static function the_posts($posts, $query)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            if (empty(Options_Service::get_global_boolean_var('enable_audiences', false))) {
                return $posts;
            }

            $wp_usr = \wp_get_current_user();

            if (self::skip_audiences($wp_usr)) {
                return $posts;
            }

            $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');
            $result = array();

            foreach ($posts as $post) {

                if (\in_array($post->post_type, $excluded_post_types)) {
                    $result[] = $post;
                    continue;
                }

                if (self::user_can_read($post->ID, $wp_usr->ID)) {
                    $result[] = $post;
                }
            }

            return $result;
        }

        /**
         * Hooked into 
         * 
         * @since   16.0
         * 
         * @see     ...
         */
        public static function get_pages($pages, $parsed_args)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            if (empty(Options_Service::get_global_boolean_var('enable_audiences', false))) {
                return $pages;
            }

            $wp_usr = \wp_get_current_user();

            if (self::skip_audiences($wp_usr)) {
                return $pages;
            }

            $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');
            $result = array();

            foreach ($pages as $page) {

                if (\in_array($page->post_type, $excluded_post_types)) {
                    $result[] = $page;
                    continue;
                }

                if (self::user_can_read($page->ID, $wp_usr->ID)) {
                    $result[] = $page;
                }
            }

            return $result;
        }

        /**
         * Hooked into 
         * 
         * @since   16.0
         * 
         * @see     ...
         */
        public static function wp_count_posts($counts, $type, $perm)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');

            if (\in_array($type, $excluded_post_types)) {
                return $counts;
            }

            foreach ($counts as $post_status => $count) {
                $query_args = array(
                    'fields'           => 'ids',
                    'post_type'        => $type,
                    'post_status'      => $post_status,
                    'numberposts'      => -1,
                    'suppress_filters' => 0,
                    'orderby'          => 'none',
                    'no_found_rows'    => true,
                    'nopaging'         => true,
                );

                $posts = get_posts($query_args);
                $count = count($posts);
                unset($posts);
                $counts->$post_status = $count;
            }

            return $counts;
        }

        /**
         * Hooked into get_adjacent_post_where filter.
         * 
         * @since   16.0
         * 
         * @see     https://developer.wordpress.org/reference/hooks/get_adjacent_post_where/
         */
        public static function get_previous_post_where($where, $in_same_term, $excluded_terms, $taxonomy, $post)
        {
            return self::get_next_post_where($where, $in_same_term, $excluded_terms, $taxonomy, $post);
        }

        /**
         * Hooked into get_adjacent_post_where filter.
         * 
         * @since   16.0
         * 
         * @see     https://developer.wordpress.org/reference/hooks/get_adjacent_post_where/
         */
        public static function get_next_post_where($where, $in_same_term, $excluded_terms, $taxonomy, $post)
        {

            if (!empty($post)) {

                $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');

                if (\in_array($post->post_type, $excluded_post_types)) {
                    return $where;
                }

                $post_ids = get_posts(array('post_type' => $post->post_type, 'numberposts' => -1, 'suppress_filters' => false, 'fields' => 'ids'));

                if (is_array($post_ids) && count($post_ids) > 0) {
                    $post_ids = array_map('intval', $post_ids);
                    $condition = ' p.ID IN (' . implode(',', $post_ids) . ') ';

                    if (!empty($where)) {
                        $where .= ' AND ' . $condition;
                    } else {
                        $where = ' WHERE ' . $condition;
                    }
                }
            }

            return $where;
        }

        /**
         * Hooked into the rest_prepare_%post_type% filter.
         * 
         * @since   16.0
         * 
         * @see     https://developer.wordpress.org/reference/hooks/rest_prepare_this-post_type/
         */
        public static function rest_prepare_post($response, $post, $request)
        {
            $wp_usr = \wp_get_current_user();

            if (self::skip_audiences($wp_usr)) {
                return $response;
            }

            $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');

            if (\in_array($post->post_type, $excluded_post_types)) {
                return $response;
            }

            if (isset($post->ID) && !self::user_can_read($post->ID, $wp_usr->ID)) {
                $response = array(
                    'code' => 'rest_post_invalid_id',
                    'message' => __('Invalid post ID.'),
                    'data' => array('status' => 404)
                );
            }

            return $response;
        }

        /**
         * Helper to register a custom column to show a user's audiences on the default WordPress Users screen.
         * 
         * @since   16.0
         * 
         * @param   Array   Array of columns
         * 
         * @return  Arry    Array of colums with optionally the "Audiences" column addded.
         */
        public static function register_users_audiences_column($columns)
        {
            $columns['wpo365_audiences'] = 'Audiences';

            return $columns;
        }

        /**
         * Helper to render the custom "Audiences" column that is added to the default WordPress Users screen.
         * 
         * @since   16.0
         * 
         * @param   string  $output         Rendered HTML
         * @param   string  $column_name    Name of the column being rendered
         * @param   string  $user_id        ID of the user the column's cell is being rendered for
         * 
         * @return  string  Rendered HTML.
         */
        public static function render_users_audiences_column($output, $column_name, $user_id)
        {
            if ('wpo365_audiences' == $column_name) {
                $audiences = Options_Service::get_global_list_var('audiences');
                $user_audiences = \get_user_meta($user_id, 'wpo365_audiences', true);

                // Not all users necessary have audiences defined for them
                if (empty($user_audiences)) {
                    return $output;
                }

                $output = '<div>';

                foreach ($user_audiences as $user_audience) {

                    foreach ($audiences as $audience) {

                        if ($audience['key'] == $user_audience) {

                            if ($output != '<div>') {
                                $output .= '<br/>';
                            }

                            $output .= '<span>' . \esc_html($audience['title']) . '</span>';
                            break;
                        }
                    }
                }

                $output .= '</div>';
            }

            return $output;
        }

        /**
         * Hooks into the add_meta_boxes action to render the custom Audiences meta box.
         * 
         * @since   19.0
         * 
         * @param   mixed   $post_type 
         * @param   mixed   $post 
         * @return  void 
         */
        public static function audiences_add_meta_box($post_type, $post)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Do nothing if the current user cannot edit the post
            if (!is_admin() || !current_user_can('edit_post', $post->ID)) {
                return;
            }

            $excluded_post_types = Options_Service::get_global_list_var('audiences_excluded_post_types');

            // Do nothing if $post_type is excluded from audiences
            if (\in_array($post_type, $excluded_post_types)) {
                return;
            }

            add_meta_box('audiences_meta_box', __('WPO365 Audiences', 'wpo365_login'), '\Wpo\Services\Audiences_Service::audiences_render_meta_box', $post_type, 'side', 'low');
        }

        /**
         * Renders the custom (HTML for the) meta box for Audiences.
         * 
         * @since   19.0
         * 
         * @return  void 
         */
        public static function audiences_render_meta_box($post)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            wp_nonce_field(basename(__FILE__), 'audiences_meta_box_nonce');
            $audiences = Options_Service::get_global_list_var('audiences');
            array_multisort(array_column($audiences, 'title'), SORT_ASC, $audiences);
            $current_audiences = get_post_meta($post->ID, 'wpo365_audiences', false);
            $currently_private = !empty(get_post_meta($post->ID, 'wpo365_private', true));

            if (empty($current_audiences)) {
                $current_audiences = array();
            }

            // Log_Service::write_log('DEBUG', sprintf('%s -> Current audiences: %s', __METHOD__, print_r($current_audiences, true)));

            $auth_scenario = Options_Service::get_global_string_var('auth_scenario');

            if (false !== WordPress_Helpers::stripos($auth_scenario, 'internet') || $currently_private) {
                echo '<p>You can make this content exclusively available for users that logged into your website (e.g. with Microsoft) by checking the box below.</p>';
                echo sprintf(
                    '<input id="wpo365Private" type="checkbox" onclick="javascript:if(document.getElementById(\'wpo365Private\').checked){document.getElementById(\'wpo365Audiences\').style.display = \'none\'}else{document.getElementById(\'wpo365Audiences\').style.display = \'initial\'}" name="wpo365_private" %s />%s<br />',
                    true === $currently_private ? 'checked' : '',
                    'Make private'
                );
                echo '<p>&nbsp;</p>';
            }

            echo sprintf(
                '<div id="wpo365Audiences" style="display: %s">',
                $currently_private ? 'none' : 'initial'
            );

            echo sprintf(
                '<p>%s can make this content exclusively available for users that are a member of one of the Audiences checked below.</p>',
                false !== WordPress_Helpers::stripos($auth_scenario, 'internet') ? 'Alternatively, you' : 'You'
            );

            foreach ($audiences as $audience) {
                echo sprintf(
                    '<input type="checkbox" name="wpo365_audiences[]" value="%s" %s />%s<br />',
                    $audience['key'],
                    in_array($audience['key'], $current_audiences) ? 'checked' : '',
                    $audience['title']
                );
            }

            echo '</div><div></div>';
        }

        /**
         * Helper to save the audiences related post meta.
         * 
         * @since   19.0
         * 
         * @param   mixed   $post_id 
         * @param   mixed   $post 
         * @param   mixed   $update 
         * @return  void 
         */
        public static function audiences_save_post($post_id, $post, $update)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Do nothing if the current user cannot edit the post
            if (!is_admin() || !current_user_can('edit_post', $post->ID)) {
                return;
            }

            // Verify meta box nonce
            if (!isset($_POST['audiences_meta_box_nonce']) || !wp_verify_nonce($_POST['audiences_meta_box_nonce'], basename(__FILE__))) {
                return;
            }

            // Always delete the post meta first
            delete_post_meta($post_id, 'wpo365_audiences');
            delete_post_meta($post_id, 'wpo365_private');

            if (isset($_POST['wpo365_audiences'])) {
                $configured_audiences = $_POST['wpo365_audiences'];
                $configured_audiences = array_map('sanitize_text_field', $configured_audiences);

                foreach ($configured_audiences as $configured_audience) {
                    add_post_meta($post_id, 'wpo365_audiences', $configured_audience, false);
                }
            }

            if (isset($_POST['wpo365_private']) && false !== WordPress_Helpers::stripos($_POST['wpo365_private'], 'on')) {
                add_post_meta($post_id, 'wpo365_private', true, true);
            }
        }

        /**
         * Hooks into the map_meta_cap function to prevent a user to directly edit a post or page
         * if he / she is not entitled to do so in the context of audiences.
         * 
         * @since   19.4
         * 
         * @param   mixed   $caps 
         * @param   mixed   $cap 
         * @param   mixed   $user_id 
         * @param   mixed   $args 
         * @return  mixed 
         */
        public static function map_meta_cap($caps, $cap, $user_id, $args)
        {
            $current_user = wp_get_current_user();

            if (self::skip_audiences($current_user)) {
                return $caps;
            }

            if (!empty($GLOBALS['post']) && $GLOBALS['post'] instanceof WP_Post && !empty($args[0])) {

                if (WordPress_Helpers::stripos($cap, 'edit_') === 0 || WordPress_Helpers::stripos($cap, 'delete_') === 0) {
                    $post_id = null;

                    if (is_numeric($args[0])) {
                        $post_id = $args[0];
                    } elseif ($args[0] instanceof WP_Post) {
                        $post_id = $args[0]->ID;
                    }

                    if ($post_id && $GLOBALS['post']->ID === $post_id) {

                        if (!self::user_can_read($post_id, $user_id)) {
                            $caps[] = 'do_not_allow';
                        }
                    }
                }
            }

            return $caps;
        }

        /**
         * Helper to check if the current user can read a specific post. A user can read a specific content item when:
         * 
         * 1. No audiences are defined and the page is not marked as private
         * 2. No audiences are defined and the page is marked as private and the user is logged in
         * 3. Audiences are defined and the user is logged in and is in one of the audiences added to the content
         * 
         * @since   16.0
         * 
         * @param   int     $post_id    The ID of the post to check
         * @param   int     wp_usr_id   The ID of the user
         * 
         * @return  bool    True if the user may read the content according to WPO365 audience rules.
         */
        private static function user_can_read($post_id, $wp_usr_id)
        {
            $current_user = wp_get_current_user();

            if (self::skip_audiences($current_user)) {
                return true;
            }

            $is_private = \get_post_meta($post_id, 'wpo365_private', true);
            $audiences = \get_post_meta($post_id, 'wpo365_audiences', false);

            // Handle audiences

            if (!empty($audiences)) {

                if ($wp_usr_id === 0) {
                    return false;
                }

                $user_audiences = \get_user_meta($wp_usr_id, 'wpo365_audiences', true);

                if (empty($user_audiences)) {
                    return false;
                }

                foreach ($audiences as $audience) {

                    if (\in_array($audience, $user_audiences)) {
                        return true;
                    }
                }

                return false;
            }

            // Handle content is private

            elseif (true === filter_var($is_private, FILTER_VALIDATE_BOOLEAN)) {

                if ($wp_usr_id > 0) {
                    return true;
                }

                return false;
            }

            return true;
        }

        /**
         * Helper to check if audiences need to be applied at all. Reasons not the apply audiences are:
         * 
         * 1. The administrator disabled support for REST and the current request is a json request
         * 2. When the current user's role has been excluded from audiences.
         * 
         * @since 16.0
         * 
         * @param   WP_User $wp_usr
         * 
         * @return  bool    True if audiences can be skipped otherwise false.
         */
        private static function skip_audiences($wp_usr)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Check if current request is json request

            if (defined('WPO365_REST_REQUEST') && WPO365_REST_REQUEST && false === Options_Service::get_global_boolean_var('enable_audiences_rest')) {
                return true;
            }

            // Check if we need to skip audiences for the current user's role

            $audiences_excluded_roles = Options_Service::get_global_list_var('audiences_excluded_roles');
            $wp_usr_roles = empty($wp_usr) ? array() : $wp_usr->roles;

            foreach ($audiences_excluded_roles as $audiences_excluded_role) {

                if (\in_array($audiences_excluded_role, $wp_usr_roles)) {
                    Log_Service::write_log('DEBUG', __METHOD__ . ' -> Skipping audiences because the user\'s role ' . $audiences_excluded_role . ' has been excluded from audience-based restrictions.');
                    return true;
                }
            }

            return false;
        }
    }
}
