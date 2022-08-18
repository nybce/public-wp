<?php

namespace Wpo\Services;

// Prevent public access to this script
defined('ABSPATH') or die();

use \Wpo\Core\User;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;
use \Wpo\Services\Graph_Service;

if (!class_exists('\Wpo\Services\User_Aad_Groups_Service')) {

    class User_Aad_Groups_Service
    {

        /**
         * Retrieves the user's AAD group memberships and adds them to the internally used User.
         * 
         * @since 11.0
         * 
         * @param   $wpo_usr    \Wpo\Core\User (by reference)
         * 
         * @return  void
         */
        public static function get_aad_groups(&$wpo_usr, $force = false, $return_response = false)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            $allowed_groups = Options_Service::get_global_list_var('groups_whitelist');
            $groups_x_roles = Options_Service::get_global_list_var('groups_x_roles');
            $groups_x_super_admins = Options_Service::get_global_list_var('mu_groups_x_super_admins');
            $groups_x_itthinx_groups = Options_Service::get_global_list_var('groups_x_groups_groups');
            $groups_x_goto_after = Options_Service::get_global_list_var('groups_x_goto_after');
            $audiences = Options_Service::get_global_list_var('audiences');

            // No aad group info is needed for this user
            if (!$force && empty($allowed_groups) && empty($groups_x_roles) && empty($groups_x_super_admins) && empty($groups_x_itthinx_groups) && empty($groups_x_goto_after) && empty($audiences)) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> No need to retrieve Azure AD groups');
                return;
            }

            $resource_identifier = !empty($wpo_usr->oid)
                ? $wpo_usr->oid
                : (
                    (!empty($wpo_usr->upn)
                        ? $wpo_usr->upn
                        : null)
                );

            $use_delegated = empty($resource_identifier);

            $query = $use_delegated
                ? '/me'
                : '/users/' . \rawurlencode($resource_identifier);

            $security_enabled_groups_only = false === Options_Service::get_global_boolean_var('all_group_memberships');

            $data = json_encode(array('securityEnabledOnly' => $security_enabled_groups_only));
            $content_length = strlen($data);
            $headers = array(
                'Accept: application/json;odata.metadata=minimal',
                'Content-Type: application/json',
                'Content-Length: ' . $content_length,
            );

            /**
             * @since   18.0    Trying first to retrieve members of an Azure AD group with a lower permission GroupMember.Read.All
             */

            $fetch_result = Graph_Service::fetch($query . '/getMemberGroups', 'POST', false, $headers, $use_delegated, true, $data, 'https://graph.microsoft.com/GroupMember.Read.All');

            if (Graph_Service::is_fetch_result_ok($fetch_result, 'Could not retrieve Azure AD group memberships (scopes tested: GroupMember.Read.All)', 'WARN')) {
                $wpo_usr->groups = array_flip($fetch_result['payload']['value']);
            } else {
                $error_message = is_wp_error($fetch_result) ? $fetch_result->get_error_message() : '';

                if (false !== stripos($error_message, 'AADSTS65001')) {
                    $fetch_result = Graph_Service::fetch($query . '/getMemberGroups', 'POST', false, $headers, $use_delegated, true, $data, 'https://graph.microsoft.com/Group.Read.All');

                    if (Graph_Service::is_fetch_result_ok($fetch_result, 'Could not retrieve Azure AD group memberships (scopes tested: GroupMember.Read.All, Group.Read.All)')) {
                        $wpo_usr->groups = array_flip($fetch_result['payload']['value']);
                    }
                } else {
                    Log_Service::write_log('ERROR', __METHOD__ . ' -> ' . $error_message . ']');
                    return false;
                }
            }

            if ($return_response) {
                return $fetch_result;
            }
        }
    }
}
