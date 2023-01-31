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
			add_filter( 'custom_permalink_before_saving', array( 'NYBC_Staff_Member', 'custom_permalink_before_saving' ), 10, 2 );
			add_action( 'admin_head', array( 'NYBC_Staff_Member', 'admin_head' ) );

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
				'area_of_research',
				array( 'staff' ),
				array(
					'labels'             => array(
						'name'              => esc_html__( 'Area of Research', 'nybc' ),
						'singular_name'     => esc_html__( 'Area of Research', 'nybc' ),
						'search_items'      => esc_html__( 'Search Area of Research', 'nybc' ),
						'all_items'         => esc_html__( 'All Areas of Research', 'nybc' ),
						'view_item '        => esc_html__( 'View Area of Research', 'nybc' ),
						'parent_item'       => esc_html__( 'Parent Area of Research', 'nybc' ),
						'parent_item_colon' => esc_html__( 'Parent Area of Research:', 'nybc' ),
						'edit_item'         => esc_html__( 'Edit Area of Research', 'nybc' ),
						'update_item'       => esc_html__( 'Update Area of Research', 'nybc' ),
						'add_new_item'      => esc_html__( 'Add Area of Research', 'nybc' ),
						'new_item_name'     => esc_html__( 'New Area of Research', 'nybc' ),
						'menu_name'         => esc_html__( 'Area of Research', 'nybc' ),
					),
					'hierarchical'       => true,
					'query_var'          => true,
					'publicly_queryable' => false,
					'public'             => true,
					'show_admin_column'  => true,
					'show_in_nav_menus'  => true,
					'show_in_rest'       => false,
					'show_ui'            => true,
					'rewrite'            => true,
				)
			);

				register_post_type(
					'staff',
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
						'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
						'menu_icon'         => null,
						'rewrite' 			=> array('slug' => 'our-research/meet-our-researchers','with_front' => false),
						'has_archive'       => false,
						'taxonomies'        => array( 'area_of_research' ),
					)
				);

		}

		/**
		 *  Modify permalink
		 *
		 * @param string  $permalink post permalink.
		 * @param integer $post_id post ID.
		 *
		 * @return string
		 */
		public static function custom_permalink_before_saving( $permalink, $post_id ) {
			$post = get_post( $post_id );
			if ( 'staff' === $post->post_type ) {
				$parent = get_field( 'parent_page' );
				if ( ! empty( $parent ) ) {
					$parent_link = get_the_permalink( $parent );
					$parent_link = str_replace( NYBC_HOME_URI, '', $parent_link );
					// @codingStandardsIgnoreStart
					$permalink   = $parent_link . sanitize_title( ! empty( $_POST['post_title'] ) ?  wp_unslash($_POST['post_title']) : $post->post_title ) . '/';
					// @codingStandardsIgnoreEnd
				}
			}

			return $permalink;
		}

		/**
		 *  Insert admin scripts
		 */
		public static function admin_head() {
			global $pagenow;
			if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) {
				return;
			}
			?>
			<script>
				jQuery(function ($) {
					$('div[data-name="parent_page"] select').on('change', function () {
						if($(this).val()) {
							$('#custom-permalinks-post-slug').val('').prop('disabled', true);
							$('#custom_permalink').val('' + Math.floor(Math.random() * 10000));
						}
					});
					$('#title').on('blur keyup', function () {
						if($(this).val()) {
							setTimeout( function() {
								$('div[data-name="parent_page"] select').change();
							}, 2000);
						}
					});
				});
			</script>
			<?php
		}

	}

	new NYBC_Staff_Member();
}
