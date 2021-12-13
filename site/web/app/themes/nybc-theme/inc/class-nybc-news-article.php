<?php
/**
 * NYBC  News Article post type class
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_News_Article ' ) ) {
	/**
	 * News Article post type class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_News_Article {


		/**
		 *  NYBC_News_Article Constructor
		 */
		public function __construct() {
			add_filter( 'post_type_labels_post', array( 'NYBC_News_Article', 'post_type_labels' ) );
			get_template_part( 'inc/acf/news-article' );

		}

		/**
		 *  Change post type labels
		 */
		public static function post_type_labels() {
			$labels = array(
				'name'               => esc_html__( 'News Articles ', 'nybc' ),
				'singular_name'      => esc_html__( 'News Article ', 'nybc' ),
				'add_new'            => esc_html__( 'Add News Article ', 'nybc' ),
				'add_new_item'       => esc_html__( 'Add News Article ', 'nybc' ),
				'edit_item'          => esc_html__( 'Edit News Article ', 'nybc' ),
				'new_item'           => esc_html__( 'New News Article ', 'nybc' ),
				'view_item'          => esc_html__( 'View News Article ', 'nybc' ),
				'search_items'       => esc_html__( 'Search News Article ', 'nybc' ),
				'not_found'          => esc_html__( 'News Articles not found', 'nybc' ),
				'not_found_in_trash' => esc_html__( 'News Articles  not found in trash', 'nybc' ),
				'parent_item_colon'  => esc_html__( 'News Article ', 'nybc' ),
				'menu_name'          => esc_html__( 'News Articles ', 'nybc' ),
			);

			return $labels;
		}
	}

	new NYBC_News_Article();
}
