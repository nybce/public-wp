<?php

    namespace Wpo\SCIM;
        
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;

    if( !class_exists( '\Wpo\SCIM\SCIM_Controller' ) ) {

        class SCIM_Controller extends \WP_REST_Controller { 

            /**
             * Register the routes for the objects of the controller.
             */
            public function register_routes() {

                // Don't register the routes if the user didn't configure SCIM
                if ( !Options_Service::get_global_boolean_var( 'enable_scim' ) ) {
                    return;
                }

                $version = '1';
                $namespace = 'wpo365/v' . $version;
                $base = 'Users';

                register_rest_route( $namespace, '/' . $base, 
                    array(
                        /* QUERY USERS */
                        array(
                            'methods'             => \WP_REST_Server::READABLE,
                            'callback'            => array( $this, 'get_items' ),
                            'permission_callback' => array( $this, 'check_permissions' ),
                            'args'                => array(
                                'filter' => array(
                                    'type' => 'string',
                                    'description' => esc_html__( 'e.g /Users?filter=userName eq "Test_User_dfeef4c5-5681-4387-b016-bdf221e82081"', 'wpo365-login' ),
                                ),
                            ),
                        ),
                        /* CREATE USER */
                        array(
                            'methods'             => \WP_REST_Server::CREATABLE,
                            'callback'            => array( $this, 'create_item' ),
                            'permission_callback' => array( $this, 'check_permissions' ),
                            'args'                => SCIM_Users::get_post_args(),
                        ),
                    )
                );
                
                register_rest_route( $namespace, '/' . $base . '/(?P<id>[^/]+)', 
                    array(
                        /* RETRIEVE USER */
                        array(
                            'methods'             => \WP_REST_Server::READABLE,
                            'callback'            => array( $this, 'get_items' ),
                            'permission_callback' => array( $this, 'check_permissions' ),
                            'args'                => array(
                                'id' => array(
                                    'type' => 'string',
                                ),
                            ),
                        ),
                        /* UPDATE USER */
                        array(
                            'methods'             => \WP_REST_Server::EDITABLE,
                            'callback'            => array( $this, 'update_item' ),
                            'permission_callback' => array( $this, 'check_permissions' ),
                            'args'                => SCIM_Users::get_patch_args(),
                        ),
                        /* DELETE USER */
                        array(
                            'methods'             => \WP_REST_Server::DELETABLE,
                            'callback'            => array( $this, 'delete_item' ),
                            'permission_callback' => array( $this, 'check_permissions' ),
                            'args'                => array(
                                'id' => array(
                                    'type' => 'string',
                                ),
                            ),
                        ),
                    )
                );
            }

            /**
             * Get a collection of items.
             * 
             * @since   10.1
             *
             * @param   WP_REST_Request $request Full data about the request.
             * 
             * @return  WP_REST_Response
             */
            public function get_items( $request ) {
                $scim_users = array();

                if( isset( $request[ 'filter' ] ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Querying for users where ' . $request[ 'filter' ] );
                    SCIM_Users::query_users( $request[ 'filter' ], $scim_users );
                    $list_response = SCIM_Users::as_list_response( $scim_users );

                    if ( is_wp_error( $list_response ) ) {
                        Log_Service::write_log( 'WARN', __METHOD__ . ' -> ' . $list_response->get_error_message() );
                        return new \WP_REST_Response( $this->error( 400, $list_response->get_error_message() ), 400 );
                    }

                    return new \WP_REST_Response( $list_response, 200 );
                }

                elseif( isset( $request[ 'id' ] ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Retrieving a user by id ' . $request[ 'id' ] );
                    SCIM_Users::get_user_by_id( $request[ 'id' ], $scim_users );

                    if ( count( $scim_users ) === 1 ) {
                        return new \WP_REST_Response( $scim_users[0], 200 );
                    }
                }

                return new \WP_REST_Response( $this->error( 404 ), 404 );
            }

            /**
             * Creates a new user from the SCIM resource present in the body of the $request.
             * 
             * @since   10.1
             * 
             * @param   $request    WP_REST_Request
             * 
             * @return  WP_REST_Response
             */
            public function create_item( $request ) {
                
                if ( !$this->is_json( $request ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Request content-type is not set to json' );
                    return new \WP_REST_Response( $this->error( 400, 'Request content-type is not set to json' ) );
                }

                try {
                    $body = $request->get_body();
                    $scim_resource = \json_decode( $body, true );
                    $scim_usr = SCIM_Users::create_user( $scim_resource );
                }
                catch( \Exception $e ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> ' . $e->getMessage() );
                    return new \WP_REST_Response( $this->error( 400, $e->getMessage() ), 400 );
                }

                if ( is_wp_error( $scim_usr ) ) {
                    return new \WP_REST_Response( $this->error( 400, $scim_usr->get_error_message() ), 400 );
                }

                return new \WP_REST_Response( $scim_usr, 201 );
            }

            /**
             * Updates an existing user using the SCIM PatchOp resource present in the body of the $request.
             * 
             * @since   10.1
             * 
             * @param   $request    WP_REST_Request
             * 
             * @return  WP_REST_Response
             */
            public function update_item( $request ) {
                
                // Try find user by external ID
                if( !isset( $request[ 'id' ] ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> ID not found' );
                    return new \WP_REST_Response( $this->error( 400, 'ID not found' ) );
                }

                // Request not properly formatted
                if ( !$this->is_json( $request ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Request content-type is not set to json' );
                    return new \WP_REST_Response( $this->error( 400, 'Request content-type is not set to json' ) );
                }

                try {
                    $body = $request->get_body();
                    $scim_ops_resource = \json_decode( $body, true );
                    
                    if ( !isset( $scim_ops_resource[ 'Operations' ] ) ) {
                        return new \WP_REST_Response( $this->error( 400, 'Could not find any Operations to update user with ID ' . $request[ 'id' ] ) );
                    }
                    
                    $scim_usr = SCIM_Users::update_user( $request[ 'id' ], $scim_ops_resource[ 'Operations' ] );
                }
                catch( \Exception $e ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> ' . $e->getMessage() );
                    return new \WP_REST_Response( $this->error( 400, $e->getMessage() ), 400 );
                }

                if ( is_wp_error( $scim_usr ) ) {
                    
                    if ( $scim_usr->get_error_code() == 'USRNOTFOUND' ) {
                        return new \WP_REST_Response( $this->error( 404, $scim_usr->get_error_message() ), 404 );
                    }
                    
                    return new \WP_REST_Response( $this->error( 400, $scim_usr->get_error_message() ), 400 );
                }

                return new \WP_REST_Response( $scim_usr, 200 );
            }

            /**
             * Deletes an existing user using the external ID sent by route.
             * 
             * @since   10.1
             * 
             * @param   $request    WP_REST_Request
             * 
             * @return  WP_REST_Response
             */
            public function delete_item( $request ) {
                
                // Try find user by external ID
                if( !isset( $request[ 'id' ] ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> ID not found' );
                    return new \WP_REST_Response( $this->error( 400, 'ID not found' ) );
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Trying to delete user with ID ' . $request[ 'id' ] );
                $result = SCIM_Users::delete_user( $request[ 'id' ] );

                if ( is_wp_error( $result ) ) {
                    
                    if ( $result->get_error_code() == 'USRNOTFOUND' ) {
                        return new \WP_REST_Response( $this->error( 404, $result->get_error_message() ), 404 );
                    }
                    
                    return new \WP_REST_Response( $this->error( 400, $result->get_error_message() ), 400 );
                }

                return new \WP_REST_Response( null, 204 );
            }

            /**
             * Check if a given request has access to get items
             *
             * @param WP_REST_Request $request Full data about the request.
             * @return WP_Error|bool
             */
            public function check_permissions( $request ) {

                if ( $this->is_valid( $request ) ) {
                    return true;
                }

                Log_Service::write_log( 'WARN', __METHOD__ . ' -> Validation of the secret token has failed' );
                return false;
            }

            /**
             * 
             */
            private function is_json( $request ) {
                $content_type = $request->get_content_type();
            
                if ( empty( $content_type ) || stripos( $content_type[ 'value' ], 'json' ) === false  ) {
                    return false;
                }

                return true;
            }

            /**
             * 
             */
            private function is_valid( $request ) {
                $headers = \getallheaders();

                if ( !isset( $headers[ 'Authorization' ] ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> No "Authorization" header was found for the incoming SCIM request. Please consult with your hosting provider whether they remove the "Authorization" header by default for security reasons.' );
                    return false;
                }

                $token = \str_replace( 'Bearer ', '', $headers[ 'Authorization' ] );

                return defined( 'WPO_SCIM_TOKEN' ) && constant( 'WPO_SCIM_TOKEN' ) == $token; 
            }

            /**
             * Helper returning a SCIM error message.
             * 
             * @since   10.1
             * 
             * @param   $status integer HTTP status code e.g. 404 for not-found or 400 for failed-operation
             * 
             * @return  array   Associative array representing a SCIM error message.
             */
            private function error( $status, $message = '' ) {
                $response = array(
                    'schemas' => 'urn:ietf:params:scim:api:messages:2.0:Error',
                    'status' => strval( $status ),
                );

                if ( !empty( $message ) ) {
                    $response[ 'detail' ] = $message;
                }

                return $response;
            }
        }
    }