<?php

    namespace Wpo\SCIM;
        
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\User;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\Request_Service;
    use \Wpo\Services\User_Service;
    use \Wpo\Services\User_Create_Update_Service;

    if ( !class_exists( '\Wpo\SCIM\SCIM_Users' ) ) {

        class SCIM_Users {

            /**
             * @since   10.1
             * 
             * @param   $query          string  Filter expression e.g. userName eq "john@doe.com" and userName eq "max@doe.com"
             * @param   &$scim_users    array   Array of SCIM users that the result will be added to
             * 
             * @return  void
             */
            public static function query_users( $query, &$scim_users ) {

                if ( !\is_array( $scim_users ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Argument exception ($scim_users is not an array)' );
                    return;
                }

                $expressions = explode( '|', \str_replace( ' and ', '|', $query ) ); // $query => userName eq "a" and userName eq "b"

                foreach ( $expressions as $expression ) { // $expression => userName eq "a"
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found the following filter expression ' . $expression );
                    $words = explode( ' ', $expression ); // $words => [ 'userName', 'eq', '"a"' ]

                    if ( count( $words ) == 3 ) {
                        $words[ 2 ] = trim( $words[ 2 ], '"');
                        
                        if ( stripos( $words[ 0 ], 'userName' ) !== false ) {
                            self::get_user_by_user_name( $words[ 2 ], $scim_users );
                        }
                        elseif ( stripos( $words[ 0 ], 'externalId' ) !== false ) {
                            self::get_user_by_external_id( $words[ 2 ], $scim_users );
                        }
                    }
                }
            }

            /**
             * @since   10.1
             * 
             * @param   $user_name      string  User (login) name
             * @param   &$scim_users    array   Array of SCIM users that the result will be added to
             * 
             * @return  void
             */
            public static function get_user_by_user_name( $user_name, &$scim_users ) {

                $wp_usr = \get_user_by( 'login', $user_name );

                if ( false !== $wp_usr ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found WP user ' . $user_name );
                    $scim_usr = self::as_scim_user( $wp_usr );

                    if ( !is_wp_error( $scim_usr ) ) {
                        $scim_users[] = $scim_usr;
                    }
                }
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find a user for ' . $user_name );
                }
            }

            /**
             * @since   10.1
             * 
             * @param   $external_id    string  External ID of the user
             * @param   &$scim_users    array   Array of SCIM users that the result will be added to
             * 
             * @return  void
             */
            public static function get_user_by_external_id( $external_id, &$scim_users ) {
                $args = array(
                    'meta_key' => 'wpo365_scim_external_id',
                    'meta_value' => $external_id,
                );

                $wp_usrs = \get_users( $args );

                if ( count( $wp_usrs )  === 1 ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found WP user ' . $wp_usrs[ 0 ]->user_login . ' for external id ' . $external_id );
                    $scim_usr = self::as_scim_user(  $wp_usrs[ 0 ] );

                    if ( !is_wp_error( $scim_usr ) ) {
                        $scim_users[] = $scim_usr;
                    }
                }
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find a user for external id ' . $external_id );
                }
            }

            /**
             * @since   10.1
             * 
             * @param   $external_id    string  Email address of the user
             * @param   &$scim_users    array   Array of SCIM users that the result will be added to
             * 
             * @return  void
             */
            public static function get_user_by_email( $email, &$scim_users ) {

                $wp_usr = \get_user_by( 'email', $email );

                if ( false !== $wp_usr ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found WP user ' . $email );
                    
                    $scim_usr = self::as_scim_user(  $wp_usr );

                    if ( !is_wp_error( $scim_usr ) ) {
                        $scim_users[] = $scim_usr;
                    }
                }
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find a user for ' . $email );
                }                
            }

            /**
             * @since   10.1
             * 
             * @param   $id             string  (WordPress) ID of the user
             * @param   &$scim_users    array   Array of SCIM users that the result will be added to
             * 
             * @return  void
             */
            public static function get_user_by_id( $id, &$scim_users ) {
                $wp_usr = \get_user_by( 'ID', $id );

                if ( false !== $wp_usr ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found WP user ' . $wp_usr->user_login . ' for id ' . $id );
                    $scim_usr = self::as_scim_user(  $wp_usr );

                    if ( !is_wp_error( $scim_usr ) ) {
                        $scim_users[] = $scim_usr;
                    }
                }
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find a user for id ' . $id );
                }
            }

            /**
             * @since   10.1
             * 
             * @param   $scim_usr   array   SCIM resource as associative array
             * 
             * @return  array|WP_Error  The WP user as SCIM resource or a WP_Error if an error occurred
             */
            public static function create_user( $scim_usr ) {

                if ( !\is_array( $scim_usr ) ) {
                    return new \WP_Error( 'ARRCHECKFAILED', 'SCIM User resource is not an associative array' );
                }

                if ( !isset( $scim_usr[ 'externalId' ] ) ) {
                    return new \WP_Error( 'CREATEUSRFAILED', 'Mandatory property externalId not found.' );
                }

                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $request->set_item( 'user_sync', true );

                $args = array(
                    'meta_key' => 'wpo365_scim_external_id',
                    'meta_value' => $scim_usr[ 'externalId' ],
                );

                $wp_usrs = \get_users( $args );

                if ( count( $wp_usrs )  > 0 ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Cannot create user with external ID ' . $scim_usr[ 'externalId' ] . ' because this ID is already in use' );
                    return new \WP_Error( 'USREXISTS', 'Cannot create user with external ID ' . $scim_usr[ 'externalId' ] . ' because this ID is already in use' );
                }

                $wpo_usr = new User();
                $wpo_usr->created = true;

                $user_mappings = self::get_user_mappings();
                
                // Process the mandatory properties
                foreach ( $user_mappings[ 0 ] as $scim_attribute => $wpo_usr_key ) {
                    $value = self::try_get_property( $scim_usr, $scim_attribute );

                    if ( !empty( $value ) && property_exists( $wpo_usr, $wpo_usr_key ) ) {
                        $wpo_usr->$wpo_usr_key = $value;
                    }
                }

                if ( empty( $wpo_usr->upn ) ) {
                    return new \WP_Error( 'CREATEUSRFAILED', 'Mandatory property userName not found.' );
                }

                // upn will be used as login_name
                $wpo_usr->preferred_username = $wpo_usr->upn;

                // unless user is an external user in which case email will be used
                if ( false !== stripos( $wpo_usr->upn, '#ext#' ) ) {

                    if ( empty( $wpo_usr->email ) ) {
                        return new \WP_Error( 'CREATEUSRFAILED', 'Cannot create WP user for guest user without email address.' );
                    }

                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found guest user ' . $wpo_usr->upn . ' and will email address ' . $wpo_usr->email . ' as user login instead' );
                    $wpo_usr->preferred_username = $wpo_usr->email;
                }

                // process the optional properties
                foreach ( $user_mappings[ 1 ] as $scim_attribute => $wpo_usr_key ) {
                    $value = self::try_get_property( $scim_usr, $scim_attribute );

                    if ( !empty( $value ) && property_exists( $wpo_usr, $wpo_usr_key ) ) {
                        $wpo_usr->$wpo_usr_key = $value;
                    }
                }

                // check if the user already is in the system
                $wp_usr = User_Service::try_get_user_by( $wpo_usr );

                // if the user is in the system then try and update instead
                if ( !empty( $wp_usr ) ) {
                    $wp_usr_id = $wp_usr->ID;
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User with login ' . $wpo_usr->preferred_username . ' already exists therefore updating details instead' );
                    self::update_wp_user( $wp_usr_id, $wpo_usr );  
                    
                    // When updating a user we want to make sure he / she is (no longer) deactivated
                    delete_user_meta( $wp_usr_id, 'wpo365_active' );
                }
                else {
                    // Create a new user
                    $wp_usr_id = User_Create_Update_Service::create_user( $wpo_usr, true, false );
                }

                // refresh wp_usr
                $wp_usr = \get_user_by( 'ID', $wp_usr_id );

                // Save user's UPN
                if ( !empty( $wpo_usr->upn ) ) {
                    update_user_meta( $wp_usr_id, 'userPrincipalName', $wpo_usr->upn );
                }

                if ( !empty( $wp_usr ) ) {

                    $user_meta_mappings = self::get_user_meta_mappings();
                
                    // Process the mandatory user meta mappings
                    foreach ( $user_meta_mappings[ 0 ] as $scim_attribute => $wp_meta_key ) {
                        self::process_user_meta_mapping( $scim_usr, $scim_attribute, $wp_usr_id, $wp_meta_key );
                    }

                    // Process the optional user meta mappings
                    foreach ( $user_meta_mappings[ 1 ] as $scim_attribute => $wp_meta_key ) {
                        self::process_user_meta_mapping( $scim_usr, $scim_attribute, $wp_usr_id, $wp_meta_key );
                    }

                    // The admin must create a mapping for (Azure AD).objectId or else enrichment may fail for guest users where the UPN is the preferred username
                    if ( ! empty( $aad_object_id = get_user_meta( $wp_usr->ID, 'aadObjectId', true ) ) ) {
                        $wpo_usr->oid = $aad_object_id;
                    }

                    // if use of an app-only token is supported update avatar and aad groups
                    if ( Options_Service::get_global_boolean_var( 'use_app_only_token' ) ) {

                        // Enrich -> Azure AD groups
                        if ( \class_exists( '\Wpo\Services\User_Aad_Groups_Service' ) && \method_exists( '\Wpo\Services\User_Aad_Groups_Service', 'get_aad_groups' ) ) {
                            \Wpo\Services\User_Aad_Groups_Service::get_aad_groups( $wpo_usr );
                        }

                        // Update Avatar
                        if ( Options_Service::get_global_boolean_var( 'use_avatar' ) && class_exists( '\Wpo\Services\Avatar_Service' ) ) {
                            $default_avatar = get_avatar( $wp_usr->ID );
                        }
                    }

                    // Update / Add roles
                    if ( \class_exists( '\Wpo\Services\User_Role_Service' ) && \method_exists( '\Wpo\Services\User_Role_Service', 'update_user_roles' ) ) {
                        \Wpo\Services\User_Role_Service::update_user_roles( $wp_usr->ID, $wpo_usr );
                    }

                    return self::as_scim_user( $wp_usr );
                }

                return new \WP_Error( 'CREATEUSRFAILED', 'Could not create user from SCIM resource. Check the log for details.' );
            }

            /**
             * @since   10.1
             * 
             * @param   $id             string  ID of the user to be updated
             * @param   $scim_ops       array   SCIM PatchOp resource as associative array
             * 
             * @return  array|WP_Error  The WP user as SCIM resource or a WP_Error if an error occurred
             */
            public static function update_user( $id, $scim_ops ) {

                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $request->set_item( 'user_sync', true );
                
                $wp_usr = \get_user_by( 'ID', intval( $id ) );

                if ( false === $wp_usr ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> WP user for id ' . $id . ' not found' );
                    return new \WP_Error( 'USRNOTFOUND', 'WP user for id ' . $id . ' not found' );
                }

                $deactivated = false;

                foreach ( $scim_ops as $operation ) {
                    
                    if ( strtolower( $operation[ 'path' ] ) == 'active' ) {
                        $deactivated = strtolower( $operation[ 'value' ] ) == 'false';

                        if ( $deactivated ) {
                            \update_user_meta( $wp_usr->ID, 'wpo365_active', 'deactivated' );

                            // Remove all roles of the deactivated user
                            foreach ( $wp_usr->roles as $current_user_role ) {
                                $wp_usr->remove_role( $current_user_role );
                            }
    
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User ' . $wp_usr->user_login . ' now ' . $deactivated );
                            break;
                        }                        
                    }
                }

                // If not deactivated then possibly the user is re-activated so remove the user's meta flag
                if ( ! $deactivated ) {
                    delete_user_meta( $wp_usr->ID, 'wpo365_active' );
                }
                
                foreach ( $scim_ops as $operation ) {

                    if ( !isset( $operation[ 'op' ] ) || !isset( $operation[ 'path' ] ) ) {
                        Log_Service::write_log( 'WARN', __METHOD__ . ' -> Operation object to patch user misses mandatory properties: ' . json_encode( $operation ) );
                        continue;
                    }

                    $remove = strtolower( $operation[ 'op' ] ) == 'remove';

                    // userName
                    if ( stripos( $operation[ 'path' ], 'userName' ) !== false ) {
                        Log_Service::write_log( 'WARN', __METHOD__ . ' -> Updating the user name for user ' . $wp_usr->user_login . ' is not supported' );
                        continue;
                    }

                    // email
                    if ( stripos( $operation[ 'path' ], 'emails' ) !== false && stripos( $operation[ 'path' ], 'work' ) !== false ) {

                        if ( !$remove && filter_var( trim( $operation[ 'value' ] ), FILTER_VALIDATE_EMAIL ) !== false ) {
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> email address updated for user ' . $wp_usr->user_login . ': ' . $operation[ 'value' ] );
                            \wp_update_user( array( 'ID' => $wp_usr->ID, 'user_email' => $operation[ 'value' ] ) );
                        }
                        else {
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Email address ' . $operation[ 'path' ] . ' for user ' . $wp_usr->user_login . ' not updated' );
                        }

                        continue;
                    }

                    // first, last and full name
                    if ( stripos( $operation[ 'path' ], 'name.' ) !== false ) {

                        if ( stripos( $operation[ 'path' ], 'name.givenName' ) !== false ) {
                            $wp_usr_key = 'first_name';
                        }
                        elseif ( stripos( $operation[ 'path' ], 'name.familyName' ) !== false ) {
                            $wp_usr_key = 'last_name';
                        }
                        elseif ( stripos( $operation[ 'path' ], 'name.formatted' ) !== false ) {
                            $wp_usr_key = 'display_name';
                        }

                        if ( !$remove && !empty( $wp_usr_key ) ) {
                            \wp_update_user( array( 'ID' => $wp_usr->ID, $wp_usr_key => $operation[ 'value' ] ) );
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> ' . $wp_usr_key . ' updated for user ' . $wp_usr->user_login . ': ' . $operation[ 'value' ] );
                        }
                        else {
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not update the user first, last or display name for user ' . $wp_usr->user_login . ' with ' . $operation[ 'value' ] );
                        }
                        
                        continue;
                    }

                    $all_user_meta_mappings = self::get_user_meta_mappings();
                    $user_meta_mappings = array_merge( $all_user_meta_mappings[ 0 ], $all_user_meta_mappings[ 1 ] );
                
                    // Process the mandatory user meta mappings
                    foreach ( $user_meta_mappings as $scim_attribute => $wp_meta_key ) {

                        $value = $operation[ 'value' ];
                        
                        if ( $scim_attribute == $operation[ 'path' ] ) {
                            
                            if ( $remove ) {
                                \delete_user_meta( $wp_usr->ID, $wp_meta_key );
                                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted ' . $scim_attribute . ' for user with ID ' . strval( $wp_usr->ID ) );
                            }
                            else {
                                // Overwrite $value with an array of manager details
                                if ( $scim_attribute == 'urn:ietf:params:scim:schemas:extension:enterprise:2.0:User:manager' && \class_exists( '\Wpo\Services\User_Custom_Fields_Service' ) && \method_exists( '\Wpo\Services\User_Custom_Fields_Service', 'get_manager_details_from_wp_user' ) ) {
                                    $value = \Wpo\Services\User_Custom_Fields_Service::get_manager_details_from_wp_user( intval( $value ) );
                                }

                                \update_user_meta( $wp_usr->ID, $wp_meta_key, $value );
                                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Updated ' . $scim_attribute . ' for user with ID ' . strval( $wp_usr->ID ) );
                            }
                        }
                    }
                }

                if ( $deactivated ) {
                    // Refresh the WP_User object after we updated it
                    $wp_usr = \get_user_by( 'ID', $wp_usr->ID );

                    // Transform WP_User to SCIM User resource
                    return self::as_scim_user( $wp_usr );
                }

                $wpo_usr = new User();
                $wpo_usr->upn = User_Service::try_get_user_principal_name( $wp_usr->ID );

                // The admin must create a mapping for (Azure AD).objectId or else enrichment may fail for guest users where the UPN is the preferred username
                if ( ! empty( $aad_object_id = get_user_meta( $wp_usr->ID, 'aadObjectId', true ) ) ) {
                    $wpo_usr->oid = $aad_object_id;
                }

                // if use of an app-only token is supported update avatar and aad groups
                if ( ! empty( $wpo_usr->upn ) && Options_Service::get_global_boolean_var( 'use_app_only_token' ) ) {

                    // Enrich -> Azure AD groups
                    if ( \class_exists( '\Wpo\Services\User_Aad_Groups_Service' ) && \method_exists( '\Wpo\Services\User_Aad_Groups_Service', 'get_aad_groups' ) ) {
                        \Wpo\Services\User_Aad_Groups_Service::get_aad_groups( $wpo_usr );
                    }

                    // Update Avatar
                    if ( Options_Service::get_global_boolean_var( 'use_avatar' ) && class_exists( '\Wpo\Services\Avatar_Service' ) ) {
                        $default_avatar = get_avatar( $wp_usr->ID );
                    }
                }

                // Update / Add roles (but not when de-activated)
                if ( \class_exists( '\Wpo\Services\User_Role_Service' ) && \method_exists( '\Wpo\Services\User_Role_Service', 'update_user_roles' ) ) {
                    \Wpo\Services\User_Role_Service::update_user_roles( $wp_usr->ID, $wpo_usr );
                }

                // Refresh the WP_User object after we updated it
                $wp_usr = \get_user_by( 'ID', $wp_usr->ID );

                // Transform WP_User to SCIM User resource
                return self::as_scim_user( $wp_usr );
            }

            /**
             * @since   10.1
             * 
             * @param   $id                 string  ID of the user to be deleted
             * 
             * @return  boolean|WP_Error    True if deleted or a WP_Error if an error occurred
             */
            public static function delete_user( $id ) {
                $wp_usr = \get_user_by( 'ID', intval( $id ) );

                if ( false === $wp_usr ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> WP user for id ' . $id . ' not found' );
                    return new \WP_Error( 'USRNOTFOUND', 'WP user for id ' . $id . ' not found' );
                }

                // The variable user_sync_allow_delete must be understood as user_sync_soft_delete instead.

                if ( ! Options_Service::get_global_boolean_var( 'user_sync_allow_delete' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/user.php' );
                    $reassign_to_id = Options_Service::get_global_numeric_var( 'scim_reassign_posts_to_id' );
                    $reassign_to = ! empty( $reassign_to_id ) ? $reassign_to_id : null;

                    return \wp_delete_user( $wp_usr->ID, $reassign_to );
                }
                else {
                    \update_user_meta( $wp_usr->ID, 'wpo365_active', 'deactivated' );

                    // Remove all roles of the deactivated user
                    foreach ( $wp_usr->roles as $current_user_role ) {
                        $wp_usr->remove_role( $current_user_role );
                    }
                }                
            }

            /**
             * @since 11.0
             */
            private static function update_wp_user( $wp_usr_id, $wpo_usr ) {

                // Update "core" WP_User fields
                $wp_user_data = array( 'ID' => $wp_usr_id );

                if ( !empty( $wpo_usr->email ) ) {
                    $wp_user_data[ 'user_email' ] = $wpo_usr->email;
                }

                if ( !empty( $wpo_usr->first_name ) ) {
                    $wp_user_data[ 'first_name' ] = $wpo_usr->first_name;
                }

                if ( !empty( $wpo_usr->last_name ) ) {
                    $wp_user_data[ 'last_name' ] = $wpo_usr->last_name;
                }

                if ( !empty( $wpo_usr->full_name ) ) {
                    $wp_user_data[ 'display_name' ] = $wpo_usr->full_name;
                }

                \wp_update_user( $wp_user_data );
            }

            /**
             * @since   10.1
             * 
             * @param   $wp_usr     The WordPress user that will be transformed into a SCIM User resource
             * @return  array|null  Associative array representing a SCIM User resource
             */
            public static function as_scim_user( $wp_usr ) {

                if ( false === $wp_usr instanceof \WP_User ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Argument is not a WP user' );
                    return new \WP_Error( 'ARGCHECKFAILED', 'Argument is not a WP user' );
                }

                $usr_meta = get_user_meta( $wp_usr->ID );

                if ( !isset( $usr_meta[ 'wpo365_scim_external_id' ] ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Cannot create a SCIM User resource for a WP user without an external ID' );
                    return new \WP_Error( 'EXIDCHECKFAILED', 'External ID for user not found' );
                }

                $active = !isset( $usr_meta[ 'wpo365_active' ] ) || $usr_meta[ 'wpo365_active' ] != 'deactivated' ? true : false;

                $scim_usr = array(
                    'schemas' => array( 'urn:ietf:params:scim:schemas:core:2.0:User' ),
                    'id' => $wp_usr->ID,
                    'meta' => array(
                        'resourceType' => 'User',
                    ),
                    'userName' => $wp_usr->user_login,
                    'name' => array(
                        'familyName' => $wp_usr->last_name,
                        'givenName' => $wp_usr->first_name,
                    ),
                    'emails' => array(
                        array(
                            'value' => $wp_usr->user_email,
                            'type' => 'work',
                            'primary' => true
                        ),
                    ),
                    'active' => $active,
                );

                $all_user_meta_mappings = self::get_user_meta_mappings();

                // Processing the mandatory and optional user meta mappings
                foreach ( $all_user_meta_mappings as $user_meta_mappings ) {
                    
                    foreach ( $user_meta_mappings as $scim_attribute => $wp_meta_key ) {
                        
                        if ( !isset( $usr_meta[ $wp_meta_key ] ) ) {
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User meta key for ' . $wp_meta_key . ' for user ' . $wp_usr->user_login . ' not found');
                            continue;
                        }

                        $usr_meta_value = is_array( $usr_meta[ $wp_meta_key ] ) && count( $usr_meta[ $wp_meta_key ] ) == 1
                            ? $usr_meta[ $wp_meta_key ][ 0 ]
                            : false;

                        if ( !empty( $usr_meta_value ) ) {
                            self::try_add_property( $scim_usr, $scim_attribute, $usr_meta_value );
                        }
                        else {
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User meta value for ' . $wp_meta_key . ' for user ' . $wp_usr->user_login . ' is empty');
                        }
                    }
                }

                return $scim_usr;
            }

            /**
             * SCIM wrapper for a collection of resources to be send as a response to a query.
             * 
             * @since 10.1
             * 
             * @param   $scim_users     array   Array with SCIM user resources
             * 
             * @return  array|WP_Error  Associative array representing a SCIM ListResponse message
             */
            public static function as_list_response( $scim_users ) {

                if ( !\is_array( $scim_users ) ) {
                    return new \WP_Error( 'ARRCHECKFAILED', 'Cannot create a list response because the argument is not an array' );
                }

                $scim_usr = array(
                    'schemas' => array( 'urn:ietf:params:scim:api:messages:2.0:ListResponse' ),
                    'totalResults' => count( $scim_users ),
                    'Resources' => $scim_users,
                    'startIndex' => 1,
                    'itemsPerPage' => 20,
                );

                return $scim_usr;
            }

            /**
             * Helper to validate a SCIM user resource that is posted to the API.
             * 
             * @since   10.1
             * 
             * @return  array   Associative array representing the minimal viable schema of a SCIM user resource
             */
            public static function get_post_args() {
                return array(
                    'schemas' => array(
                        'type' => 'array',
                        'items' => array( 
                            'type' => 'string',
                            'validate_callback' => function( $param, $request, $key ) {
                                return is_string( $param );
                            },
                        ),
                    ),
                    'externalId' => array(
                        'type' => 'string',
                        'validate_callback' => function( $param, $request, $key ) {
                            return is_string( $param );
                        },
                    ),
                    'userName' => array(
                        'type' => 'string',
                        'validate_callback' => function( $param, $request, $key ) {
                            return is_string( $param );
                        },
                    ),
                    'emails' => array(
                        'type' => 'array',
                        'items' => array(
                            'type' => 'object',
                            'properties' => array(
                                'primary' => array(
                                    'type' => 'boolean',
                                    'validate_callback' => function( $param, $request, $key ) {
                                        return is_bool( $param );
                                    },
                                ),
                                'type' => array(
                                    'type' => 'string',
                                    'validate_callback' => function( $param, $request, $key ) {
                                        return is_string( $param );
                                    },
                                ),
                                'value' => array(
                                    'type' => 'string',
                                    'validate_callback' => function( $param, $request, $key ) {
                                        return is_string( $param );
                                    },
                                ),
                            )
                            
                        )
                    ),
                    'meta' => array(
                        'type' => 'object',
                        'properties' => array(
                            'resourceType' => array(
                                'type' => 'string',
                                'validate_callback' => function( $param, $request, $key ) {
                                    return is_string( $param );
                                },
                            )
                        ),
                    ),
                    'name' => array (
                        'type' => 'object',
                        'properties' => array(
                            'familyName' => array(
                                'type' => 'string',
                                'validate_callback' => function( $param, $request, $key ) {
                                    return is_string( $param );
                                },
                            ),
                            'givenName' => array(
                                'type' => 'string',
                                'validate_callback' => function( $param, $request, $key ) {
                                    return is_string( $param );
                                },
                            )
                        ),
                    ),
                );
            }

            /**
             * Helper to validate a SCIM user resource that is patched.
             * 
             * @since   10.1
             * 
             * @return  array   Associative array representing the minimal viable schema for to PATCH a SCIM user resource
             */
            public static function get_patch_args() {
                return array(
                    'id' => array(
                        'type' => 'string',
                    ),
                    'schemas' => array(
                        'type' => 'array',
                        'items' => array( 
                            'type' => 'string',
                            'validate_callback' => function( $param, $request, $key ) {
                                return is_string( $param );
                            },
                        ),
                    ),
                    'Operations' => array(
                        'type' => 'array',
                        'items' => array(
                            'type' => 'object',
                            'properties' => array(
                                'op' => array(
                                    'type' => 'string',
                                    'validate_callback' => function( $param, $request, $key ) {
                                        return is_string( $param );
                                    },
                                ),
                                'path' => array(
                                    'type' => 'string',
                                    'validate_callback' => function( $param, $request, $key ) {
                                        return is_string( $param );
                                    },
                                ),
                            )
                        )
                    ),            
                );
            }

            private static function get_user_mappings() {
                $mandatory_user_mappings = array(
                    'emails[type eq "work"].value' => 'email',
                    'userName' => 'upn',
                );

                $optional_user_mappings = array(
                    'name.givenName' => 'first_name',
                    'name.familyName' => 'last_name',
                    'name.formatted' => 'full_name',
                );

                return array( $mandatory_user_mappings, $optional_user_mappings );
            }

            private static function get_user_meta_mappings() {
                $mandatory_user_meta_mappings = array(
                    'externalId' => 'wpo365_scim_external_id',
                );

                // The optional mappings should come from the configuration
                $scim_attribute_mappings = Options_Service::get_global_list_var( 'scim_attribute_mappings' );
                $optional_user_meta_mappings = array();

                foreach ( $scim_attribute_mappings as $scim_attribute_mapping ) {
                    $optional_user_meta_mappings[ $scim_attribute_mapping[ 'key' ] ] = $scim_attribute_mapping [ 'value' ];
                }
                
                return array( $mandatory_user_meta_mappings, $optional_user_meta_mappings );
            }

            /**
             * Processes an additional SCIM attribute given the SCIM resource, the SCIM attribute name and the WordPress User ID.
             * 
             * @since   10.1
             * 
             * @param   $scim_usr           array   Associative array representing the SCIM User resource
             * @param   $scim_attribute     string  Name of the attribute (may be formatted with a dot to depict e.g. phoneNumbers[type = mobile])
             * @param   $wp_usr_id          integer WP User ID
             * @param   $wp_meta_key        string  WP user meta key
             * @param   $delete             boolean Whether to delete the WP meta
             * 
             * @return  boolean True if the user meta was updated successfully otherwise false
             */
            private static function process_user_meta_mapping( $scim_usr, $scim_attribute, $wp_usr_id, $wp_meta_key, $delete = false ) {
                $value = self::try_get_property( $scim_usr, $scim_attribute );

                if ( ! empty( $value ) ) {

                    // Overwrite $value with an array of manager details
                    if ( $scim_attribute == 'urn:ietf:params:scim:schemas:extension:enterprise:2.0:User:manager' && \class_exists( '\Wpo\Services\User_Custom_Fields_Service' ) && \method_exists( '\Wpo\Services\User_Custom_Fields_Service', 'get_manager_details_from_wp_user' ) ) {
                        $value = \Wpo\Services\User_Custom_Fields_Service::get_manager_details_from_wp_user( intval( $value ) );
                    }

                    \update_user_meta( $wp_usr_id, $wp_meta_key, $value );
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Created ' . $scim_attribute . ' for user with ID ' . strval( $wp_usr_id ) );
                    return true;
                }

                // Set user meta as empty
                \update_user_meta( $wp_usr_id, $wp_meta_key, '' );

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not process ' . $scim_attribute . ' for user with ID ' . $wp_usr_id );
                return false;
            }

            /**
             * Simple helper to get a property from an associative array or otherwise return something "empty".
             * 
             * @since   10.1
             * 
             * @param   $resource   array   Associative array that may or may not contain the property
             * @param   $property   string  Name of the property
             */
            private static function try_get_property( $resource, $property ) {

                /**
                 * If $property is an attribute path e.g. phoneNumbers[type eq "mobile"].value
                 * then the preg_match will populate $matches as follows:
                 * 
                 * Array( 
                 *      [0] => Array ( [0] => [type eq "mobile"], [1] => 12 ),  
                 *      [1] => Array ( [0] => type eq "mobile" [1] => 13 ) scim_usr
                 * ) 
                 */

                \preg_match( '/\[(.*?)\]/', $property, $matches, PREG_OFFSET_CAPTURE );

                // $property is not formatted as a query
                if ( !is_array( $matches ) || count( $matches ) !== 2 ) {

                    if ( stripos( $property, 'urn:ietf:params:scim:schemas:extension:enterprise:2.0:User' ) === false ) {
                        $complex_property = explode( '.', $property );

                        /**
                         * If $property is an attribute path e.g. "name.givenName"
                         **/
    
                        if ( count( $complex_property ) > 0) {
                            $property_name = \end( $complex_property); // e.g. objectId
                            $attribute_name = \str_replace( ".$property_name", "", $property ); // e.g. urn:ietf:params:scim:schemas:extension:wpo365:2.0:User

                            if ( isset( $resource[ $attribute_name ] ) 
                                && is_array( $resource[ $attribute_name ] ) 
                                && isset( $resource[ $attribute_name ][ $property_name ] ) ) 
                            {
                                return $resource[ $attribute_name ][ $property_name ];
                            }
                        } 

                        /**
                         * If $property is simple property e.g. "externalId"
                         */
                        if ( isset( $resource[ $property ] ) ) {
                            return $resource[ $property ];
                        }
                    }
                    else {
                        $extension_property = str_replace( 'urn:ietf:params:scim:schemas:extension:enterprise:2.0:User:', '', $property );
                        $extension_properties = isset( $resource[ 'urn:ietf:params:scim:schemas:extension:enterprise:2.0:User' ] ) && is_array( $resource[ 'urn:ietf:params:scim:schemas:extension:enterprise:2.0:User' ] )
                            ? $resource[ 'urn:ietf:params:scim:schemas:extension:enterprise:2.0:User' ]
                            : array();

                        if ( isset( $extension_properties[ $extension_property ] ) ) {
                            return $extension_properties[ $extension_property ];
                        }
                    }

                    return false;
                }

                // $property is formatted as a query
                $query = explode( ' ', $matches[ 1 ][ 0 ] );

                if ( !is_array( $query ) || count( $query ) !== 3 ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not parse SCIM attribute path ' . $property );
                    return false;
                }

                $prop_name = substr( $property, 0, $matches[ 0 ][ 1 ] );
                $value_name = substr( $property, ( strpos( $property, '.' ) + 1 ) );

                $lookup_name = $query[0];
                $lookup_value = \str_replace( '"', '', $query[2] );

                if ( !isset( $resource[ $prop_name ] ) || !is_array( $resource[ $prop_name ] ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find SCIM attribute ' . $prop_name );
                    return false;
                }

                foreach ( $resource[ $prop_name ] as $item ) {

                    if ( isset( $item[ $lookup_name ] ) 
                        && $item[ $lookup_name ] == $lookup_value 
                        && isset( $item[ $value_name ] ) ) {
                            return $item[ $value_name ];
                    }
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find SCIM attribute ' . $prop_name . ' where ' . $lookup_name . ' equals ' . $lookup_value );
                return false;
            }

            private static function try_add_property( &$resource, $property, $value ) {
                /**
                 * See SCIM_Users::try_get_property for explanation
                 */

                \preg_match( '/\[(.*?)\]/', $property, $matches, PREG_OFFSET_CAPTURE );

                // $property is not formatted as a query
                if ( !is_array( $matches ) || count( $matches ) !== 2 ) {

                    if ( stripos( $property, 'urn:ietf:params:scim:schemas:extension:enterprise:2.0' ) == -1 ) {
                        $complex_property = explode( '.', $property );

                        /**
                         * If $property is an attribute path e.g. "name.givenName"
                         **/

                        if ( count( $complex_property ) == 2 ) {
                            
                            if ( !isset( $resource[ $complex_property [ 0 ] ] ) ) {
                                $resource[ $complex_property [ 0 ] ] = array();
                            }

                            $resource[ $complex_property [ 0 ] ][ $complex_property [ 1 ] ] = $value;
                            return;
                        }
                    }

                    /**
                     * If $property is simple property e.g. "externalId"
                     */
                    $resource[ $property ] = $value;
                    return;
                }

                // $property is formatted as a query
                $query = explode( ' ', $matches[ 1 ][ 0 ] );

                if ( !is_array( $query ) || count( $query ) !== 3 ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not parse SCIM attribute path ' . $property );
                    return;
                }

                $prop_name = substr( $property, 0, $matches[ 0 ][ 1 ] );
                $value_name = substr( $property, ( strpos( $property, '.' ) + 1 ) );

                $lookup_name = $query[0];
                $lookup_value = \str_replace( '"', '', $query[2] );

                if ( !isset( $resource[ $prop_name ] ) ) {
                    $resource[ $prop_name ] = array();
                }

                $resource[ $prop_name ][] = array(
                    $lookup_name => $lookup_value,
                    $value_name => $value
                );
            }
        }
    }