<?php
/**
 * NYBC  Staff Member post type class
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Staff_Member' ) ) {
	/**
	 * Staff Member post type class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Staff_Member {

		/**
		 *  NYBC_Staff_Member Constructor
		 */
		public function __construct() {
			add_action( 'init', array( 'NYBC_Staff_Member', 'taxonomy_post_type' ) );

			/**
			 *  Add post type fields
			 */
			get_template_part( 'inc/acf/staff-member' );
		}

		/**
		 *  Register taxonomy and post types
		 */
		public static function taxonomy_post_type() {
			register_taxonomy(
				'staff_role',
				array( 'staff_member' ),
				array(
					'labels'             => array(
						'name'              => esc_html__( 'Staff role', 'nybc' ),
						'singular_name'     => esc_html__( 'Staff role', 'nybc' ),
						'search_items'      => esc_html__( 'Search Staff roles', 'nybc' ),
						'all_items'         => esc_html__( 'All Staff roles', 'nybc' ),
						'view_item '        => esc_html__( 'View Staff role', 'nybc' ),
						'parent_item'       => esc_html__( 'Parent Staff role', 'nybc' ),
						'parent_item_colon' => esc_html__( 'Parent Staff role:', 'nybc' ),
						'edit_item'         => esc_html__( 'Edit Staff role', 'nybc' ),
						'update_item'       => esc_html__( 'Update Staff role', 'nybc' ),
						'add_new_item'      => esc_html__( 'Add Staff role', 'nybc' ),
						'new_item_name'     => esc_html__( 'New Staff role', 'nybc' ),
						'menu_name'         => esc_html__( 'Staff role', 'nybc' ),
					),
					'hierarchical'       => true,
					'query_var'          => true,
					'publicly_queryable' => true,
					'public'             => true,
					'show_admin_column'  => true,
					'show_in_nav_menus'  => true,
					'show_in_rest'       => false,
					'show_ui'            => true,
					'rewrite'            => true,
				)
			);

			register_post_type(
				'staff_member',
				array(
					'labels'            => array(
						'name'               => esc_html__( 'Staff Members', 'nybc' ),
						'singular_name'      => esc_html__( 'Staff Member', 'nybc' ),
						'add_new'            => esc_html__( 'Add Staff Member', 'nybc' ),
						'add_new_item'       => esc_html__( 'Add Staff Member', 'nybc' ),
						'edit_item'          => esc_html__( 'Edit Staff Member', 'nybc' ),
						'new_item'           => esc_html__( 'New Staff Member', 'nybc' ),
						'view_item'          => esc_html__( 'View Staff Member', 'nybc' ),
						'search_items'       => esc_html__( 'Search Staff Member', 'nybc' ),
						'not_found'          => esc_html__( 'Staff Member not found', 'nybc' ),
						'not_found_in_trash' => esc_html__( 'Staff Member not found in trash', 'nybc' ),
						'parent_item_colon'  => esc_html__( 'Staff Member', 'nybc' ),
						'menu_name'          => esc_html__( 'Staff Members', 'nybc' ),
					),
					'show_in_nav_menus' => true,
					'show_ui'           => true,
					'public'            => true,
					'show_in_rest'      => false,
					'menu_position'     => 20,
					'supports'          => array( 'title', 'editor', 'thumbnail' ),
					'menu_icon'         => null,
					'has_archive'       => false,
					'taxonomies'        => array( 'post_tag', 'staff_role' ),
				)
			);

		}
	}

	new NYBC_Staff_Member();
}
