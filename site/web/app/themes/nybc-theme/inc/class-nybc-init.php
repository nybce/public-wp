<?php
/**
 * NYBC Theme Init class
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Init' ) ) {
	/**
	 * NYBC Theme Init class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Init {

		/**
		 * Additional Image sizes
		 *
		 * @var array
		 */
		private static $image_sizes = array(
			'160x'    => array(
				'width'  => 160,
				'height' => 99999,
				'crop'   => false,
			),
			'380x325' => array(
				'width'  => 380,
				'height' => 325,
				'crop'   => true,
			),
			'380x369' => array(
				'width'  => 380,
				'height' => 369,
				'crop'   => true,
			),
			'519x283' => array(
				'width'  => 519,
				'height' => 283,
				'crop'   => true,
			),
			'519x292' => array(
				'width'  => 519,
				'height' => 292,
				'crop'   => true,
			),
			'654x367' => array(
				'width'  => 654,
				'height' => 367,
				'crop'   => true,
			),
			'800x'    => array(
				'width'  => 800,
				'height' => 99999,
				'crop'   => false,
			),
			'1062x'   => array(
				'width'  => 1062,
				'height' => 99999,
				'crop'   => false,
			),
			'1915x'   => array(
				'width'  => 1915,
				'height' => 99999,
				'crop'   => false,
			),
		);

		/**
		 *  NYBC_Init Constructor
		 */
		public function __construct() {
			if ( ! is_admin() && ! function_exists( 'get_field' ) ) {
				die( 'ACF Pro plugin required!' );
			}

			self::add_image_sizes();
			self::options_page();
			self::acf_fields();
			self::hooks();
		}

		/**
		 *  Init hooks
		 */
		public static function hooks() {
			add_action( 'after_setup_theme', array( 'NYBC_Init', 'after_setup_theme' ) );

			add_action( 'wp_enqueue_scripts', array( 'NYBC_Init', 'enqueue_scripts' ) );

			add_filter( 'intermediate_image_sizes_advanced', array( 'NYBC_Init', 'intermediate_image_sizes_advanced' ), 20, 1 );

			add_filter( 'body_class', array( 'NYBC_Init', 'body_class' ), 10, 2 );

			add_filter(
				'excerpt_length',
				function () {
					return 20;
				}
			);

			add_filter( 'posts_clauses', array( 'NYBC_Init', 'posts_clauses' ), 10, 2 );

			add_filter( 'posts_orderby', array( 'NYBC_Init', 'posts_orderby' ), 10, 2 );
			/**
			 *  Disable XML-RPC
			 */
			add_filter( 'xmlrpc_enabled', '__return_false' );
		}

		/**
		 *  Remove medium_large image size
		 *
		 * @param array $sizes sizes.
		 */
		public static function intermediate_image_sizes_advanced( $sizes ) {
			unset( $sizes['medium_large'] );

			return $sizes;
		}

		/**
		 *  Add new image sizes and update standard image sizes
		 */
		public static function add_image_sizes() {
			remove_image_size( '1536x1536' );
			remove_image_size( '2048x2048' );
			remove_image_size( 'medium_large' );
			remove_image_size( 'medium' );
			remove_image_size( 'large' );

			foreach ( self::$image_sizes as $name => $data ) {
				add_image_size( $name, $data['width'], $data['height'], $data['crop'] );
			}
		}

		/**
		 *  Disable REST API
		 */
		public static function disable_rest_api() {
			add_filter( 'rest_enabled', '__return_false' );
			remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
			remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
			remove_action( 'template_redirect', 'rest_output_link_header', 11 );
			remove_action( 'auth_cookie_malformed', 'rest_cookie_collect_status' );
			remove_action( 'auth_cookie_expired', 'rest_cookie_collect_status' );
			remove_action( 'auth_cookie_bad_username', 'rest_cookie_collect_status' );
			remove_action( 'auth_cookie_bad_hash', 'rest_cookie_collect_status' );
			remove_action( 'auth_cookie_valid', 'rest_cookie_collect_status' );
			remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );
			remove_action( 'init', 'rest_api_init' );
			remove_action( 'rest_api_init', 'rest_api_default_filters', 10 );
			remove_action( 'parse_request', 'rest_api_loaded' );
			remove_action( 'rest_api_init', 'wp_oembed_register_route' );
			remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10 );
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 */
		public static function after_setup_theme() {
			/*
			* Let WordPress manage the document title.
			*/
			add_theme_support( 'title-tag' );

			/*
			* Enable support for Post Thumbnails on posts and pages.
			*
			*/
			add_theme_support( 'post-thumbnails' );

			// This theme uses wp_nav_menu() in two locations.
			register_nav_menus(
				array(
					'main_nav'  => esc_html__( 'Main Nav', 'nybc' ),
					'page_menu' => esc_html__( 'Interior Page Menu', 'nybc' ),
				)
			);

			if ( ! is_admin() ) {
				show_admin_bar( false );
			}

		}

		/**
		 * Set up default theme scripts and styles
		 */
		public static function enqueue_scripts() {
			wp_enqueue_style( 'nybc-font-style', 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap', array(), NYBC_SCRIPT_VER );

			wp_enqueue_style( 'nybc-bootstrap-grid-style', NYBC_LIB_URI . '/css/bootstrap-grid.min.css', array(), NYBC_SCRIPT_VER );
			wp_enqueue_style( 'nybc-swiper-style', NYBC_LIB_URI . '/css/swiper.min.css', array(), NYBC_SCRIPT_VER );
			wp_enqueue_style( 'nybc-sumoselect-style', NYBC_LIB_URI . '/css/sumoselect.min.css', array(), NYBC_SCRIPT_VER );

			wp_enqueue_style( 'nybc-main-style', NYBC_ASSETS_URI . '/style.min.css', array(), NYBC_SCRIPT_VER );

			wp_enqueue_script( 'nybc-main', NYBC_ASSETS_URI . '/main.bundle.js', array( 'jquery' ), NYBC_SCRIPT_VER, true );

		}

		/**
		 * Set up ACF Options Page
		 */
		public static function options_page() {
			if ( function_exists( 'acf_add_options_page' ) ) {
				acf_add_options_page(
					array(
						'page_title' => esc_html__( 'Theme Options', 'nybc' ),
						'menu_title' => esc_html__( 'Theme Options', 'nybc' ),
						'menu_slug'  => 'theme-general-settings',
						'capability' => 'edit_posts',
						'redirect'   => false,
						'position'   => 1,
					)
				);

			}
		}

		/**
		 * Set up ACF fields
		 */
		public static function acf_fields() {
			get_template_part( 'inc/acf/theme-options' );
			get_template_part( 'inc/acf/menu-item' );

		}

		/**
		 *  Add body classes
		 *
		 * @param array $classes classes.
		 * @param array $class additional classes.
		 */
		public static function body_class( $classes, $class ) {
			if ( 'Division' === get_field( 'type', 'options' ) ) {
				$classes[] = 'division';
			}

			return $classes;
		}

		/**
		 *  Modify search query
		 *
		 * @param array  $pieces query pieces.
		 * @param object $query query.
		 *
		 * @return array
		 */
		public static function posts_clauses( $pieces, $query ) {
			global $wpdb;
			$s = $query->get( 's' );
			if ( ! empty( $s ) ) {
				$pieces['where'] = preg_replace(
					'/\(\s*' . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
					'(' . $wpdb->posts . '.post_title LIKE $1) OR (' . $wpdb->postmeta . '.meta_value LIKE $1)',
					$pieces['where']
				);

				if ( strpos( $pieces['join'], $wpdb->postmeta ) === false ) {
					$pieces['join'] .= ' LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
				}
				$pieces['groupby']  = "$wpdb->posts.ID";
				$pieces['distinct'] = 'DISTINCT';
			}
			return $pieces;
		}

		/**
		 *  Modify orderby in query
		 *
		 * @param string $orderby query pieces.
		 * @param object $query query.
		 *
		 * @return string
		 */
		public static function posts_orderby( $orderby, $query ) {
			global $wpdb;

			if ( ! is_admin() && is_search() && isset( $_GET['bydate'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'search' ) ) {
				$sort    = sanitize_sql_orderby( wp_unslash( $_GET['bydate'] ) );
				$orderby = "{$wpdb->prefix}posts.post_modified {$sort}";
			}

			return $orderby;
		}

	}

	new NYBC_Init();
}
