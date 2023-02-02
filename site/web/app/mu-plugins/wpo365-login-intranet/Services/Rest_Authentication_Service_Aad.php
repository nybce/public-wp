<?php

namespace Wpo\Services;

// Prevent public access to this script
defined('ABSPATH') or die();

use \WP_Error;
use \Wpo\Core\User;
use \Wpo\Core\WordPress_Helpers;
use \Wpo\Services\Options_Service;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Jwt_Token_Service;
use \Wpo\Services\User_Service;

if (!class_exists('\Wpo\Services\Rest_Authentication_Service_Aad')) {

    class Rest_Authentication_Service_Aad
    {
        /**
         * Handles the WordPress rest_authentication_errors hook. It looks for an Azure AD access (bearer) token in the
         * Authorization header. Apache may have stripped this away (see 
         * https://stackoverflow.com/questions/53428864/how-to-get-the-authorization-header-from-request-in-wordpress).
         * 
         * @param mixed $errors 
         * @return WP_Error|null|true
         */
        public static function authenticate_request($errors)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            // Check if we have a rule that matches the current request URI
            $wp_rest_aad_protected_endpoints = Options_Service::get_global_list_var('wp_rest_aad_protected_endpoints');
            $wp_rest_aad_application_id_uri = Options_Service::get_global_string_var('wp_rest_aad_application_id_uri');

            // Authenticated if no rules are found
            if (empty($wp_rest_aad_protected_endpoints)) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> No WordPress REST API AAD protected endpoints found');
                return null;
            }

            if (empty($wp_rest_aad_application_id_uri)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> Incomplete WordPress REST API AAD endpoint protection found [Azure AD AAD Application ID URI missing]');
                return null;
            }

            $headers = array_change_key_case(getallheaders());

            foreach ($wp_rest_aad_protected_endpoints as $wp_rest_aad_protected_endpoint) {

                if (
                    empty($wp_rest_aad_protected_endpoint['strA'])
                    || empty($wp_rest_aad_protected_endpoint['strB'])
                    || empty($wp_rest_aad_protected_endpoint['strC'])
                ) {
                    Log_Service::write_log('ERROR', __METHOD__ . '-> The following WordPress REST API AAD endpoint is invalid [' . print_r($wp_rest_aad_protected_endpoint, true) . ']');
                    continue;
                }

                // 1. REQUEST TYPE
                if (empty($_SERVER['REQUEST_METHOD']) || false === WordPress_Helpers::stripos($wp_rest_aad_protected_endpoint['strB'], $_SERVER['REQUEST_METHOD'])) {
                    Log_Service::write_log('DEBUG', __METHOD__ . '-> The type of the current request (' . $_SERVER['REQUEST_METHOD'] . ') does not match with the request type of the current rule (' . $wp_rest_aad_protected_endpoint['strB'] . ')');
                    continue;
                }

                // 2. PATH
                if (WordPress_Helpers::stripos($GLOBALS['WPO_CONFIG']['url_info']['request_uri'], $wp_rest_aad_protected_endpoint['strA']) !== false) {
                    Log_Service::write_log('DEBUG', __METHOD__ . '-> The following WordPress REST API AAD endpoint configuration will be applied [' . print_r($wp_rest_aad_protected_endpoint, true) . ']');

                    // Check if authorization header is present
                    if (empty($headers['authorization'])) {
                        Log_Service::write_log('WARN', __METHOD__ . ' -> Authorization header missing [apache or mod_security may have removed it]');

                        return new WP_Error(
                            'wpo365_rest_auth_error',
                            '403 FORBIDDEN: Authorization header was not found',
                            array('status' => 403)
                        );
                    }

                    // Prepare header and validate signature
                    $bearer = WordPress_Helpers::trim(str_ireplace('bearer', '', $headers['authorization']));
                    $claims = Jwt_Token_Service::validate_signature($bearer);

                    if (is_wp_error($claims)) {
                        Log_Service::write_log('WARN', __METHOD__ . ' Validation of the access token failed [' . $claims->get_error_message() . ']');

                        return new WP_Error(
                            'wpo365_rest_auth_error',
                            '401 UNAUTHORIZED: Access (bearer) token signature appears invalid',
                            array('status' => 401)
                        );
                    }

                    // Check scope
                    if (\property_exists($claims, 'scp')) {
                        $scope = str_replace('/', '', str_replace($wp_rest_aad_application_id_uri, '', $wp_rest_aad_protected_endpoint['strC']));

                        if (0 !== strcasecmp($scope, $claims->scp)) {
                            Log_Service::write_log('WARN', __METHOD__ . ' Validation of the access token failed [received token for a different scope: ' . $claims->scp . '  ]');

                            return new WP_Error(
                                'wpo365_rest_auth_error',
                                '401 UNAUTHORIZED: Access (bearer) token is for a different scope [' . $claims->scp . ']',
                                array('status' => 401)
                            );
                        }
                    }

                    // Impersonate current user for the request
                    $wpo_usr = new User();
                    $wpo_usr->oid = property_exists($claims, 'oid') ? $claims->oid : '';
                    $wpo_usr->upn = property_exists($claims, 'upn') ? $claims->upn : '';

                    $wp_usr = User_Service::get_user_by_oid($wpo_usr);

                    if (empty($wp_usr)) {
                        $wp_usr = User_Service::try_get_user_by($wpo_usr);

                        if (empty($wp_usr)) {
                            return new WP_Error(
                                'wpo365_rest_auth_error',
                                sprintf('401 UNAUTHORIZED: Cannot determine WordPress user [%s|%s]', $wpo_usr->oid, $wpo_usr->upn),
                                array('status' => 401)
                            );
                        }
                    }

                    if (Options_Service::get_global_boolean_var('wp_rest_set_cookies')) {
                        wp_set_auth_cookie($wp_usr->ID);
                    }

                    wp_set_current_user($wp_usr->ID);
                    Log_Service::write_log('DEBUG', sprintf('%s -> Impersonated WordPress user with ID %s ', __METHOD__, $wp_usr->ID));

                    // Exit loop as soon as the token can be validated
                    return true;
                }
            }

            // None of the rules apply -> Another authentication handler should handle this request

            if (Options_Service::get_global_boolean_var('wp_rest_block')) {
                Log_Service::write_log('WARN', sprintf('%s -> Access to this WordPress REST API is forbidden [%s]', __METHOD__, $GLOBALS['WPO_CONFIG']['url_info']['request_uri']));

                return new WP_Error(
                    'wpo365_rest_auth_error',
                    sprintf('403 FORBIDDEN: Access to this WordPress REST API is forbidden [%s]', $GLOBALS['WPO_CONFIG']['url_info']['request_uri']),
                    array('status' => 403)
                );
            }

            return null;
        }
    }
}
