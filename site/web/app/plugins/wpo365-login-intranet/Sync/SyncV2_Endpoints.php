<?php

    namespace Wpo\Sync;
        
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Sync\SyncV2_Service;

    if( !class_exists( '\Wpo\Sync\SyncV2_Endpoints' ) ) {

        class SyncV2_Endpoints { 

            /**
             * Starts the user synchronization for the job posted as the sole argument.
             */
            public static function sync_start( $rest_request ) {
                $body = $rest_request->get_json_params();

                if ( empty( $body ) || !\is_array( $body ) || empty( $body[ 'jobId' ] ) ) {
                    return new \WP_Error( 'InvalidArgumentException', __METHOD__ . ' -> Body is malformed JSON or the request header did not define the Content-type as application/json.' );
                }

                $job_id = $body[ 'jobId' ];

                $sync_result = SyncV2_Service::sync_users( $job_id );

                if ( is_wp_error( $sync_result ) ) {
                    return $sync_result;
                }
                
                return new \WP_Error( 'NoContent', '', array( 'status' => 204 ) );
            }

            /**
             * Stops the user synchronization for the job posted as the sole argument.
             */
            public static function sync_stop( $rest_request ) {
                $body = $rest_request->get_json_params();

                if ( empty( $body ) || !\is_array( $body ) || empty( $body[ 'jobId' ] ) ) {
                    return new \WP_Error( 'InvalidArgumentException', __METHOD__ . ' -> Body is malformed JSON or the request header did not define the Content-type as application/json.' );
                }

                $job_id = $body[ 'jobId' ];
                SyncV2_Service::stop( $job_id );

                return new \WP_Error( 'NoContent', '', array( 'status' => 204 ) );
            }

            /**
             * Schedules the user synchronization for the job posted as the sole argument.
             */
            public static function sync_schedule( $rest_request ) {
                $body = $rest_request->get_json_params();

                if ( empty( $body ) || !\is_array( $body ) || empty( $body[ 'jobId' ] ) ) {
                    return new \WP_Error( 'InvalidArgumentException', __METHOD__ . ' -> Body is malformed JSON or the request header did not define the Content-type as application/json.' );
                }

                $job_id = $body[ 'jobId' ];

                $scheduled = SyncV2_Service::schedule( $job_id );

                if ( is_wp_error( $scheduled ) ) {
                    return $scheduled;
                }
                
                return new \WP_Error( 'NoContent', '', array( 'status' => 204 ) );
            }

            /**
             * Unschedules the user synchronization for the job posted as the sole argument.
             */
            public static function sync_delete( $rest_request ) {
                $body = $rest_request->get_json_params();

                if ( empty( $body ) || !\is_array( $body ) || empty( $body[ 'jobId' ] ) ) {
                    return new \WP_Error( 'InvalidArgumentException', __METHOD__ . ' -> Body is malformed JSON or the request header did not define the Content-type as application/json.' );
                }

                $job_id = $body[ 'jobId' ];

                $unscheduled = SyncV2_Service::get_scheduled_events( $job_id, true );

                if ( is_wp_error( $unscheduled ) ) {
                    return $unscheduled;
                }

                SyncV2_Service::delete_job_data( $job_id );
                
                return new \WP_Error( 'NoContent', '', array( 'status' => 204 ) );
            }

            /**
             * Gets the summary of the synchronization ID.
             */
            public static function get_results_summary( $rest_request ) {
                $body = $rest_request->get_json_params();

                if ( empty( $body ) || !\is_array( $body ) || empty( $body[ 'jobId' ] ) ) {
                    return new \WP_Error( 'InvalidArgumentException', __METHOD__ . ' -> Body is malformed JSON or the request header did not define the Content-type as application/json.' );
                }

                $job_last_id = $body[ 'jobId' ];

                $job = SyncV2_Service::get_user_sync_job_by_last_id( $job_last_id );

                if ( is_wp_error( $job ) ) {
                    return $job;
                }

                $keyword = ! empty( $body[ 'keyword' ] ) ? $body[ 'keyword' ] : null;
                $status = ! empty( $body[ 'status' ] ) ? $body[ 'status' ] : null;
                
                return SyncV2_Service::get_results_summary( $job[ 'id' ], $keyword, $status );
            }

            /**
             * Gets the results for the current / last job.
             */
            public static function get_results( $rest_request ) {
                $body = $rest_request->get_json_params();

                if ( empty( $body ) || !\is_array( $body ) || empty( $body[ 'jobId' ] ) || ! is_int( $body[ 'pageSize' ] ) || ! is_int( $body[ 'offset' ] ) ) {
                    return new \WP_Error( 'InvalidArgumentException', __METHOD__ . ' -> Body is malformed JSON or the request header did not define the Content-type as application/json.' );
                }

                $job_last_id = $body[ 'jobId' ];
                $page_size = $body[ 'pageSize' ];
                $offset = $body[ 'offset' ];
                $keyword = ! empty( $body[ 'keyword' ] ) ? $body[ 'keyword' ] : null;
                $status = ! empty( $body[ 'status' ] ) ? $body[ 'status' ] : null;

                return SyncV2_Service::get_results( $job_last_id, $page_size, $offset, $keyword, $status );
            }
        }
    }