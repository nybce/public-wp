<?php
/**
 * NYBC Theme Init class
 *
 * @package NYBC
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('NYBC_Init')) {
    class NYBC_Init
    {
        static $thumbnail_size = ['width'=>100, 'height'=>100 , 'crop'=> false];
        static $medium_size = ['width'=>220, 'height'=>220 , 'crop'=> false];
        static $large_size = ['width'=>480, 'height'=>480 , 'crop'=> false];
        static $crop_thumbnail_size = ['width'=>400, 'height'=>999999 , 'crop'=> true];
        static $media_library_size = ['width'=>220, 'height'=>220 , 'crop'=> true];
        static $slick_media_size = ['width'=>853, 'height'=>480 , 'crop'=> true];
        static $wide_size = ['width'=>1090, 'height'=>999999 , 'crop'=> false];

        function __construct()
        {
            if(!is_admin() && !function_exists('get_field')){
                die('ACF Pro plugin required!');
            }

            //self::disable_rest_api();

            self::add_image_sizes();
            self::options_page();
            self::acf_fields();
            self::hooks();
        }

        /**
         *  Init hooks
         */
        static function hooks()
        {
            add_action('after_setup_theme', ['NYBC_Init', 'after_setup_theme']);
            add_action('wp_enqueue_scripts', ['NYBC_Init', 'enqueue_scripts']);
            add_action('get_footer', ['NYBC_Init', 'footer_styles']);

            add_filter('intermediate_image_sizes_advanced', ['NYBC_Init', 'intermediate_image_sizes_advanced'], 20, 1);

            /**
             *  Disable XML-RPC
             */
            add_filter('xmlrpc_enabled', '__return_false');
        }

        /**
         *  Remove medium_large image size
         */
        static function intermediate_image_sizes_advanced($sizes)
        {
            unset($sizes['medium_large']);

            return $sizes;
        }

        /**
         *  Add new image sizes and update standard image sizes
         */
        static function add_image_sizes()
        {
            remove_image_size('1536x1536');
            remove_image_size('2048x2048');

            add_image_size('thumbnail', self::$thumbnail_size['width'], self::$thumbnail_size['height'], self::$thumbnail_size['crop']);
            add_image_size('medium', self::$medium_size['width'], self::$medium_size['height'], self::$medium_size['crop']);
            add_image_size('large', self::$large_size['width'], self::$large_size['height'], self::$large_size['crop']);

            add_image_size('crop_thumbnail', self::$crop_thumbnail_size['width'], self::$crop_thumbnail_size['height'], self::$crop_thumbnail_size['crop']);
            add_image_size('media_library', self::$media_library_size['width'], self::$media_library_size['height'], self::$media_library_size['crop']);
            add_image_size('slick_media', self::$slick_media_size['width'], self::$slick_media_size['height'], self::$slick_media_size['crop']);
            add_image_size('wide', self::$wide_size['width'], self::$wide_size['height'], self::$wide_size['crop']);
        }

        /**
         *  Disable REST API
         */
        static function disable_rest_api()
        {
            add_filter('rest_enabled', '__return_false');
            remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('template_redirect', 'rest_output_link_header', 11);
            remove_action('auth_cookie_malformed', 'rest_cookie_collect_status');
            remove_action('auth_cookie_expired', 'rest_cookie_collect_status');
            remove_action('auth_cookie_bad_username', 'rest_cookie_collect_status');
            remove_action('auth_cookie_bad_hash', 'rest_cookie_collect_status');
            remove_action('auth_cookie_valid', 'rest_cookie_collect_status');
            remove_filter('rest_authentication_errors', 'rest_cookie_check_errors', 100);
            remove_action('init', 'rest_api_init');
            remove_action('rest_api_init', 'rest_api_default_filters', 10);
            remove_action('parse_request', 'rest_api_loaded');
            remove_action('rest_api_init', 'wp_oembed_register_route');
            remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10);
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
        }

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         */
        static function after_setup_theme()
        {
            /*
            * Let WordPress manage the document title.
            */
            add_theme_support('title-tag');

            /*
            * Enable support for Post Thumbnails on posts and pages.
            *
            */
            add_theme_support('post-thumbnails');


            // This theme uses wp_nav_menu() in two locations.
            register_nav_menus(
                [
                    'main_nav' => esc_html__('Main Nav', 'nybc'),
                    'page_menu' => esc_html__('Interior Page Menu', 'nybc'),
                ]
            );

        }

        /**
         * Set up default theme scripts and styles
         */
        static function enqueue_scripts()
        {
            //enqueue
            wp_enqueue_style('nybc-main-style', NYBC_ASSETS_URI . '/main.css', [], NYBC_SCRIPT_VER);
        }

        /**
         * Set up theme footer styles
         */
        static function footer_styles()
        {
            //TODO: add styles and scripts


        }

        /**
         * Set up ACF Options Page
         */
        static function options_page()
        {
        }

        /**
         * Set up ACF fields
         */
        static function acf_fields(){

        }


    }

    new NYBC_Init();
}
