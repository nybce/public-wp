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
		 * Thumbnail Size params
		 *
		 * @var array
		 */
		private static $thumbnail_size = array(
			'width'  => 100,
			'height' => 100,
			'crop'   => false,
		);

		/**
		 * Medium Size params
		 *
		 * @var array
		 */
		private static $medium_size = array(
			'width'  => 220,
			'height' => 220,
			'crop'   => false,
		);

		/**
		 * Large Size params
		 *
		 * @var array
		 */
		private static $large_size = array(
			'width'  => 480,
			'height' => 480,
			'crop'   => false,
		);

		/**
		 * Crop thumbnail size params
		 *
		 * @var array
		 */
		private static $crop_thumbnail_size = array(
			'width'  => 400,
			'height' => 999999,
			'crop'   => true,
		);

		/**
		 * Media library Size params
		 *
		 * @var array
		 */
		private static $media_library_size = array(
			'width'  => 220,
			'height' => 220,
			'crop'   => true,
		);

		/**
		 * Slick media Size params
		 *
		 * @var array
		 */
		private static $slick_media_size = array(
			'width'  => 853,
			'height' => 480,
			'crop'   => true,
		);

		/**
		 * Wide Size params
		 *
		 * @var array
		 */
		private static $wide_size = array(
			'width'  => 1090,
			'height' => 999999,
			'crop'   => false,
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
			add_action( 'get_footer', array( 'NYBC_Init', 'footer_styles' ) );

			add_filter( 'intermediate_image_sizes_advanced', array( 'NYBC_Init', 'intermediate_image_sizes_advanced' ), 20, 1 );

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

			add_image_size( 'thumbnail', self::$thumbnail_size['width'], self::$thumbnail_size['height'], self::$thumbnail_size['crop'] );
			add_image_size( 'medium', self::$medium_size['width'], self::$medium_size['height'], self::$medium_size['crop'] );
			add_image_size( 'large', self::$large_size['width'], self::$large_size['height'], self::$large_size['crop'] );

			add_image_size( 'crop_thumbnail', self::$crop_thumbnail_size['width'], self::$crop_thumbnail_size['height'], self::$crop_thumbnail_size['crop'] );
			add_image_size( 'media_library', self::$media_library_size['width'], self::$media_library_size['height'], self::$media_library_size['crop'] );
			add_image_size( 'slick_media', self::$slick_media_size['width'], self::$slick_media_size['height'], self::$slick_media_size['crop'] );
			add_image_size( 'wide', self::$wide_size['width'], self::$wide_size['height'], self::$wide_size['crop'] );
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

		}

		/**
		 * Set up default theme scripts and styles
		 */
		public static function enqueue_scripts() {
			// TODO: add styles and scripts.
		}

		/**
		 * Set up theme footer styles
		 */
		public static function footer_styles() {
			// TODO: add styles and scripts.
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

		}


	}

	new NYBC_Init();
}
