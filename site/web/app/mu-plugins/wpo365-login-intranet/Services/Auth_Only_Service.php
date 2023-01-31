<?php

namespace Wpo\Services;

use \Wpo\Core\Url_Helpers;
use \Wpo\Core\WordPress_Helpers;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Services\Auth_Only_Service')) {

    class Auth_Only_Service
    {

        /**
         * Sets a wordpress_wpo365_logged_in cookie.
         * 
         * @since   16.0
         * 
         * @return  bool    True if a cookie has been set otherwise false;
         */
        public static function set_wpo_cookie($wpo_usr, $url)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $admin_url = admin_url();
            $_admin_url = Url_Helpers::remove_protocol_and_www($admin_url);
            $_url = Url_Helpers::remove_protocol_and_www($url);

            if (WordPress_Helpers::stripos($_url, $_admin_url) === 0) {
                return false;
            }

            $cookie = self::generate_wpo_cookie($wpo_usr->preferred_username);

            $cookie_elements = explode('|', $cookie);
            $expiration = intval($cookie_elements[1]);

            $php_version = explode('.', \phpversion());

            if (intval($php_version[0]) > 7 || intval($php_version[0]) >= 7 && intval($php_version[1]) >= 3) {
                setcookie('wordpress_wpo365_logged_in', $cookie, array("expires" => $expiration, "path" => COOKIEPATH, "domain" => COOKIE_DOMAIN, "secure" => true, "httponly" => true, "SameSite" => "None"));

                if (COOKIEPATH != SITECOOKIEPATH) {
                    setcookie('wordpress_wpo365_logged_in', $cookie, array("expires" => $expiration, "path" => SITECOOKIEPATH, "domain" => COOKIE_DOMAIN, "secure" => true, "httponly" => true, "SameSite" => "None"));
                }
            } else {
                setcookie('wordpress_wpo365_logged_in', $cookie, $expiration, COOKIEPATH . "; SameSite=None", COOKIE_DOMAIN, true, true);

                if (COOKIEPATH != SITECOOKIEPATH) {
                    setcookie('wordpress_wpo365_logged_in', $cookie, $expiration, SITECOOKIEPATH . "; SameSite=None", COOKIE_DOMAIN, true, true);
                }
            }

            Log_Service::write_log('DEBUG', __METHOD__ . ' -> User ' . $wpo_usr->preferred_username . ' just authenticated successfully with OpenID Connect');

            do_action('wpo365/oidc/authenticated_only', $wpo_usr->preferred_username);

            return true;
        }

        /**
         * Sets a cookie that the user authenticated successfully but doesn't sign in that user.
         * 
         * @since   16.0
         * 
         * @param   User    $wpo_user   The (internal) user's Azure AD representation.
         * 
         * @return  string  A cookie as a string
         */
        public static function generate_wpo_cookie($preferred_username, $one_time = false)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $token = wp_generate_password(43, false, false);
            $expiration = $one_time ? time() : 0;

            if (!$one_time && Options_Service::get_global_boolean_var('remember_user')) {
                $expiration = time() + 14 * DAY_IN_SECONDS;
            }

            $key = wp_hash($preferred_username . '|' . $expiration . '|' . $token, 'secure_auth');
            $algo = function_exists('hash') ? 'sha256' : 'sha1';
            $hash = hash_hmac($algo, $preferred_username . '|' . $expiration . '|' . $token, $key);
            $cookie = $preferred_username . '|' . $expiration . '|' . $token . '|' . $hash;

            return $cookie;
        }

        /**
         * Validates the authentication cookie the plugin generated when the
         * plugin is configured to only perform authentication but no sign-in 
         * of an Azure AD user.
         * 
         * @since   16.0
         * 
         * @return  bool    True if the auth cookie appears valid otherwise false.
         * 
         */
        public static function validate_auth_cookie()
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $scenario = Options_Service::get_global_string_var('auth_scenario');

            if ($scenario != 'internetAuthOnly' && $scenario != 'intranetAuthOnly') {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> Trying to perform cookie authentication but the selected authentication scenario does not support cookie authentication [' . $scenario . '].');
                return false;
            }

            if (is_admin() || is_network_admin()) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Cookie authentication is not performed for WP-Admin.');
                return false;
            }

            if (!empty($_COOKIE['wordpress_wpo365_logged_in'])) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Found wordpress_wpo365_logged_in cookie [$_COOKIE].');
                $cookie = $_COOKIE['wordpress_wpo365_logged_in'];
            } elseif (!empty($_GET['wordpress_wpo365_logged_in'])) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Found wordpress_wpo365_logged_in cookie [$_GET].');
                $cookie = urldecode($_GET['wordpress_wpo365_logged_in']);
            } elseif (!empty($_COOKIE[constant('TEST_COOKIE')])) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Found WordPress\'s own wordpress_test_cookie.');
                return false;
            } else {
                Log_Service::write_log('WARN', __METHOD__ . ' -> No cookies detected. This is most likely an issue caused by server-side PHP / WordPress page caching.');
                Log_Service::write_log('DEBUG', $GLOBALS['WPO_CONFIG']['url_info']);
                $current_url = $GLOBALS['WPO_CONFIG']['url_info']['current_url'];
                $redirect_to = Url_Helpers::get_preferred_login_url();
                $redirect_to = add_query_arg('redirect_to', $current_url, $redirect_to);
                $redirect_to = add_query_arg('wpo_redirect', true, $redirect_to);
                Url_Helpers::force_redirect($redirect_to);
                // -> exit();
            }

            $cookie_elements = explode('|', $cookie);

            if (count($cookie_elements) !== 4) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> Found a bad authentication cookie [wrong format: ' . $cookie . '].');
                return false;
            }

            list($username, $expiration, $token, $hmac) = $cookie_elements;

            // Check if cookie is expired
            if ($expiration > 10 && $expiration < time()) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Found a wordpress_wpo365_logged_in cookie that is expired for user ' . $username);

                if (!empty($_GET['wordpress_wpo365_logged_in'])) {
                    $url_with_cookie_removed = remove_query_arg('wordpress_wpo365_logged_in');
                    Url_Helpers::force_redirect($url_with_cookie_removed);
                    // -> exit()
                }

                return false;
            }

            $key = wp_hash($username . '|' . $expiration . '|' . $token, 'secure_auth');
            $algo = function_exists('hash') ? 'sha256' : 'sha1';
            $hash = hash_hmac($algo, $username . '|' . $expiration . '|' . $token, $key);

            // Check if cookie's hash is not bad
            if (!hash_equals($hash, $hmac)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> Found a bad authentication cookie for user ' . $username . ' [wrong hash].');
                return false;
            }

            header('Cache-Control: private, max-age=1');

            return true;
        }

        /**
         * Action that is called from skip_authentication when executed for the
         * login page since most caches won't cache the login page and thus cookies
         * can be read. In this case the wordpress_wpo365_logged_in is retrieved
         * and added as to the request instead.
         * 
         * @since   16.0
         * 
         * @return  void
         */
        public static function cookie_redirect()
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $scenario = Options_Service::get_global_string_var('auth_scenario');

            if (
                !empty($_GET['wpo_redirect']) &&
                Url_Helpers::is_wp_login() &&
                !empty($_COOKIE['wordpress_wpo365_logged_in'])
            ) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Auth-only scenario detected and wordpress_wpo365_logged_in found.');
                $cookie = \urldecode($_COOKIE['wordpress_wpo365_logged_in']);
                $cookie_elements = explode('|', $cookie);

                if (count($cookie_elements) == 4) {
                    $redirect_to = $_GET['redirect_to'];
                    $redirect_to = add_query_arg('wordpress_wpo365_logged_in', \urlencode(self::generate_wpo_cookie($cookie_elements[0], true)), $redirect_to);
                    Url_Helpers::force_redirect($redirect_to);
                    // -> exit();
                }

                Log_Service::write_log('ERROR', __METHOD__ . ' -> Found a bad authentication cookie [wrong format: ' . $cookie . ']');
            }
        }

        /**
         * Removes the wordpress_wpo365_logged_in query var.
         * 
         * @since   16.0
         * 
         * @param   string  $redirect_to    The URL to be filtered.
         * 
         * @return  string  The $redirect_to URL with the wordpress_wpo365_logged_in query arg removed.
         */
        public static function remove_cookie_from_url($redirect_to)
        {
            return remove_query_arg('wordpress_wpo365_logged_in', $redirect_to);
        }
    }
}
