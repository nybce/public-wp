<?php

namespace Wpo\Services;

use \Wpo\Core\WordPress_Helpers;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Services\Pkce_Service')) {

    /**
     * See https://www.oauth.com/oauth2-servers/pkce/authorization-request/
     * @package Wpo\Services
     */
    class Pkce_Service
    {
        /**
         * Creates a code verifier as meant for PKCE.
         * 
         * @since 18.0
         * 
         * @param mixed $len The length of the code that will be generated.
         * @return string The code verifier
         */
        public static function generate_pkce_verifier($len)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $len = $len < 43 ? 43 : ($len > 128 ? 128 : $len);
            return WordPress_Helpers::base64_url_encode(openssl_random_pseudo_bytes($len));
        }

        /**
         * Takes the code verifier and creates a hash for it that is sent along with the request for an authorization token.
         * 
         * @param mixed $verifier String that is the (base64 URL encoded version of the) code verifier.
         * @return string[] An array with the code_challenge and a code_challenge_id that is the key for the verifier code when 
         *                  it's stored in the database and which is sent along as a suffix for the state parameter.
         */
        public static function generate_pkce_challenge($verifier)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Create the code verifier challenge as hash for the code verifier
            $pkce_code_challenge = WordPress_Helpers::base64_url_encode(hash('SHA256', $verifier, true));

            // Create the database key for the code verifier as the last 10 characters of the hash for the pkce_code_challenge
            $pkce_code_challenge_id = substr(WordPress_Helpers::base64_url_encode(hash('SHA256', $pkce_code_challenge, true)), -12, 10);

            // Get the site option used to store the code verifiers that are not claimed
            $pkce_code_verifiers = get_network_option(null, 'wpo365_pkce_code_verifiers', array());
            $pkce_code_verifiers[$pkce_code_challenge_id] = $verifier;

            // Keep the last 250 - 500 generated code verifiers
            for ($i = sizeof($pkce_code_verifiers); $i >= 500; $i--) {
                array_splice($pkce_code_verifiers, 0, 250);
            }

            // Save it back to the site option
            update_network_option(null, 'wpo365_pkce_code_verifiers', $pkce_code_verifiers);

            return array(
                'pkce_code_challenge' => $pkce_code_challenge,
                'pkce_code_challenge_id' => $pkce_code_challenge_id,
            );
        }

        /**
         * This helper finds the code verifier in the cache of unclaimed code verifiers.
         * 
         * @since 18.0
         * 
         * @param mixed $pkce_code_challenge_id 
         * @return null 
         */
        public static function find_personal_pkce_code_verifier($pkce_code_challenge_id, $remove = true)
        {
            $pkce_code_verifier = null;

            // Get the site option used to store the code verifiers that are not claimed
            $pkce_code_verifiers = get_network_option(null, 'wpo365_pkce_code_verifiers', array());

            if (!empty($pkce_code_verifiers[$pkce_code_challenge_id])) {
                $pkce_code_verifier = $pkce_code_verifiers[$pkce_code_challenge_id];
                unset($pkce_code_verifiers[$pkce_code_challenge_id]);
            }

            // Save it back to the site option
            update_network_option(null, 'wpo365_pkce_code_verifiers', $pkce_code_verifiers);

            return $pkce_code_verifier;
        }

        /**
         * Helper method to persist a pkce verifier code as user meta.
         * 
         * @since 18.0
         * 
         * @param string $pkce PKCE Verifier Code as string
         * @return void
         */
        public static function save_personal_pkce_code_verifier($pkce_code_verifier)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $wp_usr_id = get_current_user_id();

            if (empty($wp_usr_id)) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Cannot save PKCE verifier code for user that is not logged in.');
                return;
            }

            update_user_meta(
                $wp_usr_id,
                'wpo365_pkce_code_verifier',
                $pkce_code_verifier
            );

            Log_Service::write_log('DEBUG', __METHOD__ . ' -> Successfully saved PKCE verifier code');
        }

        /**
         * Helper method to get a persisted pkce verifier code from user meta.
         * 
         * @since 18.0
         * 
         * @return string|false Returns the user's personal verifier code from user meta or false if not found.
         */
        public static function get_personal_pkce_code_verifier()
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // If the ID token was processed in this request then the PKCE verifier code has not yet been persisted.

            $request_service = Request_Service::get_instance();
            $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
            $pkce_code_verifier = $request->get_item('pkce_code_verifier');

            if (!empty($pkce_code_verifier)) {
                return $pkce_code_verifier;
            }

            // If it wasn't found in the request's cache then it must be saved

            $wp_usr_id = get_current_user_id();

            if (empty($wp_usr_id)) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Cannot get a PKCE verifier code for user that is not logged in.');
                return false;
            }

            $pkce_code_verifier = get_user_meta(
                $wp_usr_id,
                'wpo365_pkce_code_verifier',
                true
            );

            Log_Service::write_log('DEBUG', sprintf('%s -> %s to retrieve PKCE verifier code', __METHOD__, (empty($pkce_code_verifier) ? 'Failed' : 'Succeeded')));

            return $pkce_code_verifier; // May be false if not found
        }

        /**
         * Helper that takes the $params for the OpenID Connect flow URL and
         * adds the code challenge to it.
         * 
         * @since   18.0
         * 
         * @param   array   $params     Parameters for the OpenID Connect flow URL
         * @return  void
         */
        public static function add_and_memoize_verifier(&$params)
        {
            $challenge_and_id = self::generate_pkce_challenge(
                self::generate_pkce_verifier(56)
            );
            $params['code_challenge'] = $challenge_and_id['pkce_code_challenge'];
            $params['code_challenge_method'] = 'S256';

            // Challenge identifier is added to state so it can be used to look up the code verifier when the user returns from Microsoft
            $params['state'] = sprintf('%s_____%s', $params['state'], $challenge_and_id['pkce_code_challenge_id']);
        }

        /**
         * Helper to sanitize the state and retrieve the code verifier when 
         * the user is redirected back from Microsoft.
         * 
         * @since   18.0
         * 
         * @return string 
         */
        public static function process_state_with_verifier()
        {
            $request_service = Request_Service::get_instance();
            $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
            $state = $request->get_item('state');

            if (false !== WordPress_Helpers::stripos($state, '_____')) {
                $state_info = explode('_____', $state);
                $pkce_code_challenge_id = $state_info[1];
                $pkce_code_verifier = \Wpo\Services\Pkce_Service::find_personal_pkce_code_verifier($pkce_code_challenge_id);

                if (!empty($pkce_code_verifier)) {
                    $request->set_item('pkce_code_verifier', $pkce_code_verifier);
                    Log_Service::write_log('DEBUG', sprintf('%s -> Found persisted PKCE code verifier [%s]', __METHOD__, $pkce_code_challenge_id));
                } else {
                    Log_Service::write_log('WARN', sprintf('%s -> Could not find PKCE code verifier [%s]', __METHOD__, $pkce_code_challenge_id));
                }

                return $state_info[0];
            } else {
                Log_Service::write_log('WARN', sprintf('%s -> Could not find PKCE code challenge ID in state [%s]', __METHOD__, $state));
            }

            return $state;
        }
    }
}
