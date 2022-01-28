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
		 * Site ID for additional search
		 *
		 * @var int
		 */
		private static $site_id_for_additional_search = null;

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
			self::acf_fields();
			self::add_image_sizes();
			self::options_page();
			self::hooks();

		}

		/**
		 *  Init hooks
		 */
		public static function hooks() {
			add_action( 'after_setup_theme', array( 'NYBC_Init', 'after_setup_theme' ) );

			add_action( 'wp_enqueue_scripts', array( 'NYBC_Init', 'enqueue_scripts' ) );

			add_action( 'pre_get_posts', array( 'NYBC_Init', 'pre_get_posts' ) );

			add_filter( 'the_posts', array( 'NYBC_Init', 'the_posts' ), 10, 2 );

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

			add_action( 'init', array( 'NYBC_Init', 'init' ), 100 );

			add_filter( 'dt_pull_capabilities', array( 'NYBC_Init', 'dt_pull_capabilities' ) );

			add_filter( 'dt_push_capabilities', array( 'NYBC_Init', 'dt_push_capabilities' ) );

			add_filter( 'dt_capabilities', array( 'NYBC_Init', 'dt_pull_capabilities' ) );

			add_filter( 'dt_syndicatable_capabilities', array( 'NYBC_Init', 'dt_push_capabilities' ) );

			/**
			 *  Disable XML-RPC
			 */
			add_filter( 'xmlrpc_enabled', '__return_false' );

		}

		/**
		 *  Modify roles and status
		 */
		public static function init() {
			global $wp_roles;

			unset( $wp_roles->roles['editor'] );
			unset( $wp_roles->roles['subscriber'] );
			unset( $wp_roles->roles['custom_permalinks_manager'] );

			$wp_roles->roles['author']['name']      = esc_html__( 'Content Editor', 'nybc' );
			$wp_roles->roles['contributor']['name'] = esc_html__( 'Content Publisher', 'nybc' );

			get_role( 'administrator' )->add_cap( 'distributor_pull_content' );
			get_role( 'contributor' )->add_cap( 'distributor_pull_content' );

			get_role( 'contributor' )->add_cap( 'distributor_push_content', false );

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
			$tag_index = array_search( 'tag', $classes, true );
			if ( $tag_index ) {
				unset( $classes[ $tag_index ] );
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

		/**
		 *  Modify query
		 *
		 * @param object $query query.
		 */
		public static function pre_get_posts( $query ) {

			if ( ! is_admin() && is_multisite() ) {
				self::$site_id_for_additional_search = (int) get_field( 'site_id_for_additional_search', 'options' );
				if ( ! get_blog_details( self::$site_id_for_additional_search ) ) {
					self::$site_id_for_additional_search = null;
				}
			}

			if ( ! is_admin() && is_multisite() && self::$site_id_for_additional_search && is_search() && $query->is_main_query() ) {
				$curr_page = $query->get( 'paged' );
				$query->set( '_paged', $curr_page ? $curr_page : 1 );
				$query->set( 'posts_per_page', -1 );
				$query->set( 'paged', 1 );
			}

			if ( $query->is_archive() && ! is_search() && $query->is_main_query() ) {
				$query->set( 'post_type', array( 'post', 'story' ) );
			}

			if ( $query->is_archive() && ! is_search() && $query->is_main_query() && isset( $_GET['terms'] )
				&& ! empty( $_GET['terms'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'filter' ) ) {

				$selected_terms = sanitize_text_field( wp_unslash( $_GET['terms'] ) );
				$terms          = explode( ',', $selected_terms );
				$tax_query      = $query->get( 'tax_query' ) ? $query->get( 'tax_query' ) : array();
				// @codingStandardsIgnoreStart
				if (!empty($terms)) {
					$tax_query[] =
						array(
							'taxonomy' => 'category',
							'terms' => $terms,
						);
				}
				// @codingStandardsIgnoreEnd

				$query->set( 'tax_query', $tax_query );
			}
			if ( ( $query->is_archive() || is_home() ) && ! is_search() && $query->is_main_query() && isset( $_GET['bydate'] ) && ! empty( $_GET['bydate'] )
				&& isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'filter' ) ) {

				$bydate = sanitize_text_field( wp_unslash( $_GET['bydate'] ) );
				$date   = explode( '/', $bydate );
				if ( 1 === count( $date ) ) {
					array_unshift( $date, '01' );
				}
				$start = "{$date[1]}-{$date[0]}-01";
				$end   = gmdate( 'Y-m-t', strtotime( $start ) );

				$date_query = array(
					array(
						'after'     => $start,
						'before'    => $end,
						'inclusive' => true,
					),
				);
				$query->set( 'date_query', $date_query );
			}

		}

		/**
		 *  Modify posts result
		 *
		 * @param object $posts result posts.
		 * @param object $query query.
		 *
		 * @return array
		 */
		public static function the_posts( $posts, $query ) {
			global $wp_query;
			if ( ! is_admin() && is_multisite() ) {
				self::$site_id_for_additional_search = (int) get_field( 'site_id_for_additional_search', 'options' );
				if ( ! get_blog_details( self::$site_id_for_additional_search ) ) {
					self::$site_id_for_additional_search = null;
				}
			}

			if ( ! is_admin() && is_multisite() && self::$site_id_for_additional_search && is_search() && $query->is_main_query() ) {
				$posts_per_page = get_option( 'posts_per_page' );
				$curr_page      = $query->get( '_paged' );
				$curr_site_id   = get_current_blog_id();

				if ( self::$site_id_for_additional_search !== $curr_site_id ) {
					$site_id = self::$site_id_for_additional_search;
					switch_to_blog( $site_id );

					$posts_site = get_posts(
						array(
							'suppress_filters' => false,
							's'                => $query->get( 's' ),
							'posts_per_page'   => -1,
							'post_type'        => array(
								'post',
								'page',
								'story',
								'staff',
							),
						)
					);
					$posts_site = array_map(
						function ( $p ) use ( $site_id ) {
							$p->site_id = $site_id;
							return $p;
						},
						$posts_site
					);
					$posts      = array_merge( $posts, is_array( $posts_site ) ? $posts_site : array() );
					restore_current_blog();
				}

				if ( isset( $_GET['bydate'] ) && ! empty( $_GET['bydate'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'search' ) ) {
					$sort = sanitize_sql_orderby( wp_unslash( $_GET['bydate'] ) );
					usort(
						$posts,
						function( $a, $b ) use ( $sort ) {
							return 'ASC' === $sort ? strtotime( $a->post_modified ) - strtotime( $b->post_modified ) : strtotime( $b->post_modified ) - strtotime( $a->post_modified );
						}
					);
				}
				$wp_query->found_posts   = count( $posts );
				$wp_query->max_num_pages = ceil( $wp_query->found_posts / $posts_per_page );

				$posts = array_slice( $posts, ( $curr_page - 1 ) * $posts_per_page, $posts_per_page );

				$wp_query->set( 'posts_per_page', $posts_per_page );
				$wp_query->set( 'paged', $curr_page );
			}
			return $posts;
		}

		/**
		 *  Modify Distributor push capabilities
		 *
		 * @param string $cap capability name.
		 *
		 * @return string
		 */
		public static function dt_push_capabilities( $cap ) {
			$curr_user_id = get_current_user_id();

			if ( ! is_super_admin( $curr_user_id ) ) {
				$cap = 'distributor_push_content';
			}

			return $cap;
		}

		/**
		 *  Modify Distributor pull capabilities
		 *
		 * @param string $cap capability name.
		 *
		 * @return string
		 */
		public static function dt_pull_capabilities( $cap ) {
			$curr_user_id = get_current_user_id();

			if ( ! is_super_admin( $curr_user_id ) ) {
				$cap = 'distributor_pull_content';
			}

			return $cap;
		}

	}



	new NYBC_Init();
}
