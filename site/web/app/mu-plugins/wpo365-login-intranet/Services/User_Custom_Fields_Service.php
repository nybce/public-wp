<?php

namespace Wpo\Services;

// Prevent public access to this script
defined('ABSPATH') or die();

use \Wpo\Core\WordPress_Helpers;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;
use \Wpo\Services\Graph_Service;
use \Wpo\Services\Saml2_Service;
use \Wpo\Services\User_Service;
use \Wpo\Services\User_Details_Service;

if (!class_exists('\Wpo\Services\User_Custom_Fields_Service')) {

    class User_Custom_Fields_Service
    {
        /**
         * @since 11.0
         */
        public static function update_custom_fields($wp_usr_id, $wpo_usr)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            if (empty($wpo_usr->graph_resource)) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Cannot update custom user fields because the graph resource has not been retrieved');
                return;
            }

            // Check to see if expanded properties need to be loaded (currently only manager is supported)
            $extra_user_fields = Options_Service::get_global_list_var('extra_user_fields');
            $expanded_fields = array();

            // Iterate over the configured graph fields and identify any supported expandable properties
            array_map(function ($kv_pair) use (&$expanded_fields) {
                if (false !== WordPress_Helpers::stripos($kv_pair['key'], 'manager')) {
                    $expanded_fields[] = 'manager';
                }
            }, $extra_user_fields);

            // Query to expand property
            if (in_array('manager', $expanded_fields)) {
                $upn = User_Service::try_get_user_principal_name($wp_usr_id);

                if (!empty($upn)) {
                    $user_manager = Graph_Service::fetch('/users/' . \rawurlencode($upn) . '/manager', 'GET', false, array('Accept: application/json;odata.metadata=minimal'));

                    // Expand user details
                    if (Graph_Service::is_fetch_result_ok($user_manager, 'Could not retrieve user manager details for user ' . $upn, 'WARN')) {
                        $wpo_usr->graph_resource['manager'] = $user_manager['payload'];
                    }
                }
            }

            self::process_extra_user_fields(function ($name, $title) use (&$wpo_usr, &$wp_usr_id) {

                $parsed_user_field_key = User_Details_Service::parse_user_field_key($name);
                $name = $parsed_user_field_key[0];
                $wp_user_meta_key = $parsed_user_field_key[1];

                $name_arr = explode('.', $name);
                $current = $wpo_usr->graph_resource;
                $value = null;

                if (sizeof($name_arr) > 1) {

                    $found = false;

                    for ($i = 0; $i < sizeof($name_arr); $i++) {
                        /**
                         * Found must be true for the last iteration therefore it resets every cycle
                         */

                        $found = false;

                        /**
                         * Administrator has specified to get the nth element of an array / object
                         */

                        if (is_array($current) && \is_numeric($name_arr[$i]) && $i < sizeof($current) && array_key_exists($name_arr[$i], $current)) {
                            $current = $current[$name_arr[$i]];
                            $found = true;
                        }

                        /**
                         * Administrator has specified to get the named element of an array / object
                         */

                        else if (is_array($current) && array_key_exists($name_arr[$i], $current)) {
                            $current = $current[$name_arr[$i]];
                            $found = true;
                        }
                    }

                    if ($found) {
                        $value = $current;
                    }
                }

                /**
                 * Administrator has specified a simple non-nested property
                 */

                else if (array_key_exists($name, $current) && !empty($current[$name])) {
                    $value = $name == 'manager'
                        ? self::parse_manager_details($current['manager'])
                        : $current[$name];
                }

                if (empty($value)) {
                    $value = '';
                }

                update_user_meta(
                    $wp_usr_id,
                    $wp_user_meta_key,
                    $value
                );

                if (function_exists('xprofile_set_field_data') && true === Options_Service::get_global_boolean_var('use_bp_extended')) {
                    xprofile_set_field_data($title, $wp_usr_id, $value);
                }
            });
        }

        /**
         * Processes the extra user fields and tries to read them from the SAML attributes 
         * and if found saves their value as WordPress user meta.
         * 
         * @since   20.0
         * 
         * @param   mixed   $wp_usr_id 
         * @param   mixed   $wpo_usr 
         * @return  void 
         */
        public static function update_custom_fields_from_saml_attributes($wp_usr_id, $wpo_usr)
        {
            Log_Service::write_log('DEBUG', '##### -> ' . __METHOD__);

            if (empty($wpo_usr->saml_attributes)) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Cannot update custom user fields because the SAML attributes are not found');
                return;
            }

            self::process_extra_user_fields(function ($name, $title) use (&$wpo_usr, &$wp_usr_id) {

                $parsed_user_field_key = User_Details_Service::parse_user_field_key($name);
                $claim = $parsed_user_field_key[0];
                $wp_user_meta_key = $parsed_user_field_key[1];

                if (strcmp($claim, $wp_user_meta_key) === 0 && WordPress_Helpers::stripos($wp_user_meta_key, '/') > 0) {
                    $key_exploded = explode('/', $wp_user_meta_key);
                    $wp_user_meta_key = sprintf('saml_%s', array_pop($key_exploded));
                }

                $value = Saml2_Service::get_attribute($claim, $wpo_usr->saml_attributes);

                update_user_meta(
                    $wp_usr_id,
                    $wp_user_meta_key,
                    $value
                );

                if (function_exists('xprofile_set_field_data') && true === Options_Service::get_global_boolean_var('use_bp_extended')) {
                    xprofile_set_field_data($title, $wp_usr_id, $value);
                }
            });
        }

        /**
         * 
         * @param function callback with signature ( $name, $title ) => void
         * 
         * @return void
         */
        public static function process_extra_user_fields($callback)
        {
            $extra_user_fields = Options_Service::get_global_list_var('extra_user_fields');

            if (sizeof($extra_user_fields) == 0)
                return;

            foreach ($extra_user_fields as $kv_pair)
                $callback($kv_pair['key'], $kv_pair['value']);
        }

        /**
         * Adds an additional section to the bottom of the user profile page
         * 
         * @since 2.0
         * 
         * @param WP_User $user whose profile is being shown
         * @return void
         */
        public static function show_extra_user_fields($user)
        {
            if (false === Options_Service::get_global_boolean_var('graph_user_details')) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Extra user fields disabled as per configuration');
                return;
            } elseif (true === Options_Service::get_global_boolean_var('use_bp_extended')) {
                Log_Service::write_log('DEBUG', __METHOD__ . ' -> Extra user fields will be display on BuddyPress Extended Profile instead');
                return;
            } else {

                echo ("<h3>" . __('Office 365 Profile Information', 'wpo365-login') . "</h3>");
                echo ("<table class=\"form-table\">");

                self::process_extra_user_fields(function ($name, $title) use (&$user) {

                    $parsed_user_field_key = User_Details_Service::parse_user_field_key($name);
                    $name = $parsed_user_field_key[0];
                    $wp_user_meta_key = $parsed_user_field_key[1];

                    // The following may be true for SAML based custom attributes
                    if (strcmp($name, $wp_user_meta_key) === 0 && WordPress_Helpers::stripos($wp_user_meta_key, '/') > 0) {
                        $key_exploded = explode('/', $wp_user_meta_key);
                        $wp_user_meta_key = sprintf('saml_%s', array_pop($key_exploded));
                    }

                    $value = get_user_meta($user->ID, $wp_user_meta_key, true);

                    echo ('<tr><th><label for="' . esc_attr($wp_user_meta_key) . '">' . esc_html($title) . '</label></th>');

                    if (is_array($value)) {

                        echo ("<td>");

                        foreach ($value as $idx => $val) {

                            if (empty($val)) {
                                continue;
                            }

                            echo '<input type="text" name="' . esc_attr($wp_user_meta_key) . '__##__' . esc_attr($idx) . '" id="' . esc_attr($wp_user_meta_key) . esc_attr($idx) . '" value="' . esc_attr($val) . '" class="regular-text" /><br />';
                        }

                        echo ("</td>");
                    } else {

                        echo ('<td><input type="text" name="' . esc_attr($wp_user_meta_key) . '" id="' . esc_attr($wp_user_meta_key) . '" value="' . esc_attr($value) . '" class="regular-text" /><br/></td>');
                    }

                    echo ("</tr>");
                });

                echo ('</table>');
            }
        }

        /**
         * Allow users to save their updated extra user fields
         * 
         * @since 4.0
         * 
         * @return mixed(boolean|void)
         */
        public static function save_user_details($user_id)
        {
            if (!current_user_can('edit_user', $user_id)) {
                return false;
            }

            self::process_extra_user_fields(function ($name, $title) use (&$user_id) {

                $parsed_user_field_key = User_Details_Service::parse_user_field_key($name);
                $name = $parsed_user_field_key[0];
                $wp_user_meta_key = $parsed_user_field_key[1];

                // The following may be true for SAML based custom attributes
                if (strcmp($name, $wp_user_meta_key) === 0 && WordPress_Helpers::stripos($wp_user_meta_key, '/') > 0) {
                    $key_exploded = explode('/', $wp_user_meta_key);
                    $wp_user_meta_key = sprintf('saml_%s', array_pop($key_exploded));
                }

                $lookup = str_replace('.', '_', $wp_user_meta_key); // '.' is changed to '_' when sent in a request

                if (isset($_POST[$lookup])) {

                    update_user_meta(
                        $user_id,
                        $wp_user_meta_key,
                        sanitize_text_field($_POST[$lookup])
                    );
                    return;
                }

                $array_of_user_meta = array();

                foreach ($_POST as $key => $value) {

                    if (false !== WordPress_Helpers::strpos($key, $lookup . "__##__")) {
                        $array_of_user_meta[$key] = $value;
                    }
                }

                if (false === empty($array_of_user_meta)) {

                    $array_of_user_meta_values = array_values($array_of_user_meta);

                    update_user_meta(
                        $user_id,
                        $wp_user_meta_key,
                        $array_of_user_meta_values
                    );
                    return;
                }
            });
        }

        /**
         * Gets details of a user as an array with displayName, mail, officeLocation, department, 
         * businessPhones, mobilePhone.
         * 
         * @since   15.0
         * 
         * @param   int     $wp_usr_id
         * @return  array
         */
        public static function get_manager_details_from_wp_user($wp_usr_id)
        {

            if (empty($wp_usr = get_user_by('ID', $wp_usr_id))) {
                return array();
            }

            $displayName = $wp_usr->display_name;
            $mail = $wp_usr->user_email;
            $officeLocation = get_user_meta($wp_usr_id, 'officeLocation', true);
            $department = get_user_meta($wp_usr_id, 'department', true);
            $businessPhones = get_user_meta($wp_usr_id, 'businessPhones', true);
            $mobilePhone = get_user_meta($wp_usr_id, 'mobilePhone', true);

            return array(
                'displayName' => $displayName,
                'mail' => $mail,
                'officeLocation' => !empty($officeLocation) ? $officeLocation : '',
                'department' => !empty($department) ? $department : '',
                'businessPhones' => !empty($businessPhones) ? $businessPhones : '',
                'mobilePhone' => !empty($mobilePhone) ? $mobilePhone : '',
            );
        }

        /**
         * Parses the manager details fetched from Microsoft Graph.
         * 
         * @since 7.17
         * 
         * @return array Assoc. array with the most important manager details.
         */
        private static function parse_manager_details($manager)
        {
            if (empty($manager)) {
                return array();
            }
            $displayName = !empty($manager['displayName'])
                ? $manager['displayName']
                : '';
            $mail = !empty($manager['mail'])
                ? $manager['mail']
                : '';
            $officeLocation = !empty($manager['officeLocation'])
                ? $manager['officeLocation']
                : '';
            $department = !empty($manager['department'])
                ? $manager['department']
                : '';
            $businessPhones = !empty($manager['businessPhones'])
                ? $manager['businessPhones'][0]
                : '';
            $mobilePhone = !empty($manager['mobilePhone'])
                ? $manager['mobilePhone'][0]
                : '';
            return array(
                'displayName' => $displayName,
                'mail' => $mail,
                'officeLocation' => $officeLocation,
                'department' => $department,
                'businessPhones' => $businessPhones,
                'mobilePhone' => $mobilePhone,
            );
        }
    }
}
