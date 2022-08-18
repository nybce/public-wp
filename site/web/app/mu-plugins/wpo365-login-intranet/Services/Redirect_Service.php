<?php

namespace Wpo\Services;

use \Wpo\Core\Url_Helpers;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Services\Redirect_Service')) {

    class Redirect_Service
    {

        /**
         * Helper method to determine the redirect URL which can either be the last page
         * the user visited before authentication stored in the posted state property, or
         * if configured the goto_after_signon_url or in case none of these apply the WordPress
         * home URL, or if configured an URL based on one of the AD groups the user is a 
         * member of. This method can be called from the wpo_redirect_url filter.
         * 
         * @since 7.1
         * 
         * @return string URL to send the user once authentication completed
         */
        public static function get_redirect_url($site_url, $group_ids = array(), $is_new_user = false)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            /**
             * @since 9.1
             * 
             * Check whether the user has just been created and if so check
             * to see whether the user should be redirected to a welcome 
             * page.
             */

            if ($is_new_user) {
                $welcome_page_url = Options_Service::get_global_string_var('welcome_page_url');

                if (!empty($welcome_page_url)) {
                    return $welcome_page_url;
                }
            }

            $goto_after_signon_url = Options_Service::get_global_string_var('goto_after_signon_url');

            // In case the always use goto after was configured
            if (true === Options_Service::get_global_boolean_var('always_use_goto_after')) {

                if (!empty($goto_after_signon_url))
                    return $goto_after_signon_url;
            }

            // Helper to determine if the argument contains the custom error page URL
            $is_error_page = function ($compare_with_url) {
                $error_page_url = Options_Service::get_global_string_var('error_page_url');

                if (!empty($error_page_url) && !empty($compare_with_url)) {
                    return false !== stripos($compare_with_url, $error_page_url);
                }

                return false;
            };

            $request_service = Request_Service::get_instance();
            $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
            $state_url = $request->get_item('state');
            $relay_state_url = $request->get_item('relay_state');

            if (!empty($state_url)) {
                $redirect_url = (false === Url_Helpers::is_wp_login($state_url) && false === $is_error_page($state_url))
                    ? $state_url
                    : (!empty($goto_after_signon_url)
                        ? $goto_after_signon_url
                        : $site_url
                    );
            } elseif (!empty($relay_state_url)) {
                $redirect_url = (false === Url_Helpers::is_wp_login($relay_state_url) && false === $is_error_page($relay_state_url))
                    ? $relay_state_url
                    : (!empty($goto_after_signon_url)
                        ? $goto_after_signon_url
                        : $site_url
                    );
            } else {
                $redirect_url = $site_url;
            }

            // In case a mapping between Azure AD groups and redirect URLs was configured
            if (\is_array($group_ids) && count($group_ids) > 0) {
                $groups_x_goto_after = Options_Service::get_global_list_var('groups_x_goto_after');

                foreach ($groups_x_goto_after as $index => $kv_pair) {

                    if (
                        array_key_exists($kv_pair['key'], $group_ids) &&
                        !empty($kv_pair['value'])
                    ) {
                        return $kv_pair['value'];
                    }
                }
            }

            return $redirect_url;
        }

        /**
         * Logout without confirmation to support single sign-out.
         * 
         * @since 9.4
         * 
         * @param $action Action verb from query string
         * @param $result 
         *
         * @return void
         */
        public static function logout_without_confirmation($action, $result)
        {

            if (false === Options_Service::get_global_boolean_var('enable_single_sign_out')) {
                return;
            }

            if ($action == 'log-out' && !isset($_GET['_wpnonce'])) {
                $redirect_to = isset($_REQUEST['redirect_to'])
                    ? esc_url_raw($_REQUEST['redirect_to'])
                    : '';
                $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));
                header("Location: $location");
                die();
            }
        }
    }
}
