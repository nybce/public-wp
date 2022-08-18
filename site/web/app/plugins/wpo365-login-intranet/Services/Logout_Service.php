<?php

namespace Wpo\Services;

use \Wpo\Core\Url_Helpers;
use \Wpo\Services\Error_Service;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

if (!class_exists('\Wpo\Services\Logout_Service')) {

    class Logout_Service
    {

        /**
         * Hooks into a default logout action and additionally logs out the user from Office 365 before sending
         * the user to the default login page.
         * 
         * @since 3.1
         * 
         * @return void
         */
        public static function logout_O365()
        {

            if (Options_Service::get_global_boolean_var('logout_from_o365')) {
                $post_logout_redirect_uri = Options_Service::get_global_string_var('post_signout_url');

                if (empty($post_logout_redirect_uri)) {
                    $post_logout_redirect_uri = Url_Helpers::get_preferred_login_url();
                }

                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
                $wpo_auth_value = $request->get_item('wpo_auth_value');

                $logout_url = !empty($wpo_auth_value)
                    ? "https://login.microsoftonline.com/common/oauth2/logout?post_logout_redirect_uri=$post_logout_redirect_uri"
                    : $post_logout_redirect_uri;

                Url_Helpers::force_redirect($logout_url);
            }
        }

        /**
         * Hooks into a default logout action and sends the user to a custom "error" page in case the
         * Administrator has enabled SSO for the login page.
         * 
         * @since 12.x
         * 
         * @return void
         */
        public static function send_to_custom_logout_page()
        {

            if (Options_Service::get_global_boolean_var('redirect_on_login')) {
                $logged_out_url = Options_Service::get_global_string_var('error_page_url');

                if (empty($logged_out_url)) {
                    Log_Service::write_log('ERROR', __METHOD__ . ' -> Administrator has enabled SSO for the login page but not configured an error page to send the user when he / she logs out');
                    return;
                }

                $logged_out_url = add_query_arg('login_errors', Error_Service::LOGGED_OUT, $logged_out_url);

                Url_Helpers::force_redirect($logged_out_url);
            }
        }
    }
}
