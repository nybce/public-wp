<?php

namespace Wpo\Sync;

// Prevent public access to this script
defined('ABSPATH') or die();

use \Wpo\Core\Permissions_Helpers;
use \Wpo\Sync\SyncV2_Endpoints;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

if (!class_exists('\Wpo\Sync\SyncV2_Controller')) {

    class SyncV2_Controller extends \WP_REST_Controller
    {

        /**
         * Register the routes for the objects of the controller.
         */
        public function register_routes()
        {

            $version = '1';
            $namespace = 'wpo365/v' . $version;

            register_rest_route(
                $namespace,
                '/sync/start',
                array(
                    array(
                        'methods' => \WP_REST_Server::CREATABLE,
                        'callback' => function ($request) {
                            return SyncV2_Endpoints::sync_start($request);
                        },
                        'permission_callback' => array($this, 'check_permissions'),
                    ),
                )
            );

            register_rest_route(
                $namespace,
                '/sync/stop',
                array(
                    array(
                        'methods' => \WP_REST_Server::CREATABLE,
                        'callback' => function ($request) {
                            return SyncV2_Endpoints::sync_stop($request);
                        },
                        'permission_callback' => array($this, 'check_permissions'),
                    ),
                )
            );

            register_rest_route(
                $namespace,
                '/sync/schedule',
                array(
                    array(
                        'methods' => \WP_REST_Server::CREATABLE,
                        'callback' => function ($request) {
                            return SyncV2_Endpoints::sync_schedule($request);
                        },
                        'permission_callback' => array($this, 'check_permissions'),
                    ),
                )
            );

            register_rest_route(
                $namespace,
                '/sync/delete',
                array(
                    array(
                        'methods' => \WP_REST_Server::CREATABLE,
                        'callback' => function ($request) {
                            return SyncV2_Endpoints::sync_delete($request);
                        },
                        'permission_callback' => array($this, 'check_permissions'),
                    ),
                )
            );

            register_rest_route(
                $namespace,
                '/sync/results/summary',
                array(
                    array(
                        'methods' => \WP_REST_Server::CREATABLE,
                        'callback' => function ($request) {
                            return SyncV2_Endpoints::get_results_summary($request);
                        },
                        'permission_callback' => array($this, 'check_permissions'),
                    ),
                )
            );

            register_rest_route(
                $namespace,
                '/sync/results',
                array(
                    array(
                        'methods' => \WP_REST_Server::CREATABLE,
                        'callback' => function ($request) {
                            return SyncV2_Endpoints::get_results($request);
                        },
                        'permission_callback' => array($this, 'check_permissions'),
                    ),
                )
            );
        }

        /**
         * Checks if the user can retrieve an access token for the requested scope.
         * 
         * @param string $scope Scope for which the token must be valid.
         * @return bool|WP_Error True if user can retrieve an access token for the requested scope otherwise a WP_Error is returned.
         */
        public function check_permissions($request)
        {

            if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
                return new \WP_Error('UnauthorizedException', 'The request cannot be validated.', array('status' => 401));
            }

            $wp_usr = \wp_get_current_user();

            if (empty($wp_usr)) {
                return new \WP_Error('UnauthorizedException', 'Please sign in first before using this API.', array('status' => 401));
            }

            if (!Permissions_Helpers::user_is_admin($wp_usr)) {
                return new \WP_Error('UnauthorizedException', 'Please sign in with administrative credentials before using this API.', array('status' => 403));
            }

            return true;
        }
    }
}
