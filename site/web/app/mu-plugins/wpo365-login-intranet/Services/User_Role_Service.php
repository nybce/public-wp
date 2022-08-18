<?php

    namespace Wpo\Services;

    use \Wpo\Core\Permissions_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    if ( !class_exists( '\Wpo\Services\User_Role_Service' ) ) {

        class User_Role_Service {

            public static function update_user_roles( $wp_usr_id, $wpo_usr ) {
                Log_Service::write_log( 'DEBUG', '##### -> ' . __METHOD__ );

                if ( Options_Service::get_global_boolean_var( 'enable_audiences' ) && class_exists( '\Wpo\Services\Audiences_Service' ) ) {
                    // Optionally update audience assignments
                    \Wpo\Services\Audiences_Service::aad_group_x_audience( $wp_usr_id, $wpo_usr );
                }

                if ( class_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service' ) && method_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service', 'aad_group_x_itthinx_group' ) ) {
                    // Optionally update itthinx group assignments
                    \Wpo\Services\Mapped_Itthinx_Groups_Service::aad_group_x_itthinx_group( $wp_usr_id, $wpo_usr );
                }

                if ( class_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service' ) && method_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service', 'custom_field_x_itthinx_group' ) ) {
                    // Optionally update itthinx group assignments
                    \Wpo\Services\Mapped_Itthinx_Groups_Service::custom_field_x_itthinx_group( $wp_usr_id, $wpo_usr );
                }

                $update_strategy = strtolower( Options_Service::get_global_string_var( 'replace_or_update_user_roles' ) );

                if ( $update_strategy == 'skip' ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Target role for user could not be determined because the administrator configured the plugin to not update user roles' );
                    return;
                }

                // Get all possible roles for user
                $user_roles = self::get_user_roles( $wp_usr_id, $wpo_usr );

                $wp_usr = \get_user_by( 'ID', $wp_usr_id );

                if ( ! Options_Service::get_global_boolean_var( 'update_admins' ) && ( \in_array( 'administrator', $wp_usr->roles ) || is_super_admin( $wp_usr_id ) ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Not updating the role for a user that is already an administrator.' );
                    return;
                }

                $usr_default_role = is_main_site() 
                    ? Options_Service::get_global_string_var( 'new_usr_default_role' ) 
                    : Options_Service::get_global_string_var( 'mu_new_usr_default_role' );

                // Remove default role -> It will be added later if requested as fallback
                if ( in_array( $usr_default_role, $wp_usr->roles ) ) {
                    $wp_usr->remove_role( $usr_default_role );
                    // refresh the user meta for
                    $wp_usr = \get_user_by( 'ID', $wp_usr_id );
                }

                // Empty any existing roles when configured to do so
                if ( $update_strategy == 'replace' ) {
                    foreach ( $wp_usr->roles as $current_user_role ) {
                        $wp_usr->remove_role( $current_user_role );
                    }

                    // refresh the user meta for
                    $wp_usr = \get_user_by( 'ID', $wp_usr_id );
                }

                // Add from new roles if not already added
                foreach ( $user_roles as $user_role ) {
                    if ( false === in_array( $user_role, $wp_usr->roles ) ) {
                        $wp_usr->add_role( $user_role );
                    }
                }

                // refresh the user meta for
                $wp_usr = \get_user_by( 'ID', $wp_usr_id );

                // Add default role if needed / configured
                if ( empty( $wp_usr->roles ) || ( !empty( $wp_usr->roles ) && false === Options_Service::get_global_boolean_var( 'default_role_as_fallback' ) ) ) {
                    
                    if ( !empty( $usr_default_role ) ) {
                        $usr_default_role = strtolower( $usr_default_role );
                        $wp_role = get_role( $usr_default_role );

                        if ( empty( $wp_role ) ){
                            Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Trying to add the default role but it appears undefined' );
                        }
                        else {
                            $wp_usr->add_role( $usr_default_role );
                        }
                    }
                }
            }

            /**
             * Gets the user's default role or if a mapping exists overrides that default role 
             * and returns the role according to the mapping.
             * 
             * @since 3.2
             * 
             * 
             * @return mixed(array|WP_Error) user's role as string or an WP_Error if not defined
             */
            private static function get_user_roles( $wp_usr_id, $wpo_usr ) {
                $user_roles = array();

                // Graph user resource property x WP role
                if ( class_exists( '\Wpo\Services\Mapped_Custom_Fields_Service' ) && method_exists( '\Wpo\Services\Mapped_Custom_Fields_Service', 'custom_field_x_role' ) ) {
                    \Wpo\Services\Mapped_Custom_Fields_Service::custom_field_x_role( $user_roles, $wpo_usr );
                }

                // AAD group x WP role
                if ( class_exists( '\Wpo\Services\Mapped_Aad_Groups_Service' ) && method_exists( '\Wpo\Services\Mapped_Aad_Groups_Service', 'aad_group_x_role' ) ) {
                    \Wpo\Services\Mapped_Aad_Groups_Service::aad_group_x_role( $user_roles, $wpo_usr );
                }

                // AAD group x Super Admin
                if ( class_exists( '\Wpo\Services\Mapped_Aad_Groups_Service' ) && method_exists( '\Wpo\Services\Mapped_Aad_Groups_Service', 'aad_group_x_super_admin' ) ) {
                    \Wpo\Services\Mapped_Aad_Groups_Service::aad_group_x_super_admin( $wp_usr_id, $wpo_usr, true );
                }
                
                // Logon Domain x WP role
                if ( class_exists( '\Wpo\Services\Mapped_Domains_Service' ) && method_exists( '\Wpo\Services\Mapped_Domains_Service', 'domain_x_role' ) ) {
                    \Wpo\Services\Mapped_Domains_Service::domain_x_role( $user_roles, $wpo_usr );
                }

                return $user_roles;
            }
        }
    }