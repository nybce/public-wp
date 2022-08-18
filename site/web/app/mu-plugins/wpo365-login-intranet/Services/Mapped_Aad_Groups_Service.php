<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Mapped_Aad_Groups_Service' ) ) {

        class Mapped_Aad_Groups_Service {

            /**
             * @since 11.0
             */
            public static function aad_group_x_role( &$user_roles, $wpo_usr ) {
                // Add new roles as per AD Group > WP role mapping
                $group_role_settings = Options_Service::get_global_list_var( 'groups_x_roles' );

                foreach ( $group_role_settings as $kv_pair ) {
                    if ( array_key_exists( $kv_pair[ 'key' ], $wpo_usr->groups ) ) {
                        $role_from_role_mapping = strtolower( $kv_pair[ 'value' ] );

                        // Check if the role exists (if not it is not added)
                        if ( null === get_role( $role_from_role_mapping ) ) {
                            Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Group mapping for WordPress role ' . $role_from_role_mapping .' was found for user ' . $wpo_usr->preferred_username . ' but this role does not exist in WordPress' );
                            continue;
                        }

                        // Only add new WordPress role
                        if ( false === in_array( $role_from_role_mapping, $user_roles ) ) {
                            $user_roles[] = $role_from_role_mapping;
                            Log_Service::write_log( 'DEBUG', __METHOD__ . " -> Found group mapping for WordPress role ' . $role_from_role_mapping .' and added it to the user's roles array" );
                        }                        
                    }
                }
            }

            /**
             * Promotes a user to Super Admin if that user is in one of the mapped Azure AD groups.
             * 
             * @since 15.0
             * 
             * @param   string  $wp_usr_id  The user's WordPress ID
             * @param   User    $wpo_usr    The user's internal representation
             * @param   boolean $revoke     If true the user's Super Admin privileges (if any) will be revoked
             * 
             * @return  void
             */
            public static function aad_group_x_super_admin( $wp_usr_id, $wpo_usr ) {

                if ( !is_multisite() ) {
                    return;
                }

                $groups_x_super_admins = Options_Service::get_global_list_var( 'mu_groups_x_super_admins' );
                $revoke = Options_Service::get_global_boolean_var( 'mu_revoke_super_admin' );
                $granted = false;

                foreach( $groups_x_super_admins as $aad_group_id ) {

                    if ( array_key_exists( $aad_group_id, $wpo_usr->groups ) ) {
                        $grant_result = grant_super_admin( $wp_usr_id );
                        $granted = true;

                        if ( ! $grant_result ) {
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not grant user with ID ' . $wp_usr_id . ' Super Admin privileges' );
                        }
                        else {
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Granted user with ID ' . $wp_usr_id . ' Super Admin privileges' );
                        }
                    }
                }

                if ( sizeof( $groups_x_super_admins ) > 0 && ! $granted && $revoke ) {
                    revoke_super_admin( $wp_usr_id );
                }
            }
        }
    }
