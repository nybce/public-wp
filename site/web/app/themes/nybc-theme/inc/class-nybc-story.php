<?php
/**
 * NYBC Story post type class
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Story ' ) ) {
	/**
	 * Story post type class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Story {

		/**
		 *  NYBC_Story Constructor
		 */
		public function __construct() {
			add_action( 'init', array( 'NYBC_Story', 'taxonomy_post_type' ) );

		}

		/**
		 *  Register taxonomy and post types
		 */
		public static function taxonomy_post_type() {

			register_post_type(
				'story',
				array(
					'labels'            => array(
						'name'               => esc_html__( 'Stories ', 'nybc' ),
						'singular_name'      => esc_html__( 'Story', 'nybc' ),
						'add_new'            => esc_html__( 'Add Story ', 'nybc' ),
						'add_new_item'       => esc_html__( 'Add Story ', 'nybc' ),
						'edit_item'          => esc_html__( 'Edit Story ', 'nybc' ),
						'new_item'           => esc_html__( 'New Story ', 'nybc' ),
						'view_item'          => esc_html__( 'View Story ', 'nybc' ),
						'search_items'       => esc_html__( 'Search Stories ', 'nybc' ),
						'not_found'          => esc_html__( 'Stories not found', 'nybc' ),
						'not_found_in_trash' => esc_html__( 'Stories not found in trash', 'nybc' ),
						'parent_item_colon'  => esc_html__( 'Story', 'nybc' ),
						'menu_name'          => esc_html__( 'Stories', 'nybc' ),
					),
					'show_in_nav_menus' => true,
					'show_ui'           => true,
					'public'            => true,
					'show_in_rest'      => true,
					'menu_position'     => 20,
					'supports'          => array( 'title', 'editor', 'thumbnail' ),
					'menu_icon'         => null,
					'has_archive'       => false,
					'taxonomies'        => array( 'post_tag', 'category' ),
				)
			);
		}

	}

	new NYBC_Story();
}
