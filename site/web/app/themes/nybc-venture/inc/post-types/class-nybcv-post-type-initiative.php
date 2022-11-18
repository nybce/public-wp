<?php
/**
 * Custom post type for Initiatives
 *
 */

/**
 * Class for the initiative post type.
 */
class NYBCV_Post_Type_Initiative extends NYBCV_Post_Type {

	/**
	 * Name of the custom post type.
	 *
	 * @var string
	 */
	public $name = 'initiative';

	/**
	 * Creates the post type.
	 */
	public function create_post_type() {
		register_post_type(
			$this->name,
			[
				'labels' => [
					'name'                     => __( 'Initiatives', 'nybcv' ),
					'singular_name'            => __( 'Initiative', 'nybcv' ),
					'add_new'                  => __( 'Add New Initiative', 'nybcv' ),
					'add_new_item'             => __( 'Add New Initiative', 'nybcv' ),
					'edit_item'                => __( 'Edit Initiative', 'nybcv' ),
					'new_item'                 => __( 'New Initiative', 'nybcv' ),
					'view_item'                => __( 'View Initiative', 'nybcv' ),
					'view_items'               => __( 'View Initiatives', 'nybcv' ),
					'search_items'             => __( 'Search Initiatives', 'nybcv' ),
					'not_found'                => __( 'No initiatives found', 'nybcv' ),
					'not_found_in_trash'       => __( 'No initiatives found in Trash', 'nybcv' ),
					'parent_item_colon'        => __( 'Parent Initiative:', 'nybcv' ),
					'all_items'                => __( 'All Initiatives', 'nybcv' ),
					'archives'                 => __( 'Initiative Archives', 'nybcv' ),
					'attributes'               => __( 'Initiative Attributes', 'nybcv' ),
					'insert_into_item'         => __( 'Insert into initiative', 'nybcv' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this initiative', 'nybcv' ),
					'featured_image'           => __( 'Featured Image', 'nybcv' ),
					'set_featured_image'       => __( 'Set featured image', 'nybcv' ),
					'remove_featured_image'    => __( 'Remove featured image', 'nybcv' ),
					'use_featured_image'       => __( 'Use as featured image', 'nybcv' ),
					'filter_items_list'        => __( 'Filter initiatives list', 'nybcv' ),
					'items_list_navigation'    => __( 'Initiatives list navigation', 'nybcv' ),
					'items_list'               => __( 'Initiatives list', 'nybcv' ),
					'item_published'           => __( 'Initiative published.', 'nybcv' ),
					'item_published_privately' => __( 'Initiative published privately.', 'nybcv' ),
					'item_reverted_to_draft'   => __( 'Initiative reverted to draft.', 'nybcv' ),
					'item_scheduled'           => __( 'Initiative scheduled.', 'nybcv' ),
					'item_updated'             => __( 'Initiative updated.', 'nybcv' ),
					'menu_name'                => __( 'Initiatives', 'nybcv' ),
				],
				'public' => true,
				'menu_icon' => 'dashicons-book',
				'rewrite' => [
					'slug' => 'initiatives',
				],
				'show_in_rest' => true,
				'supports' => [ 'title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields' ],
			]
		);
	}
}
$nybcv_post_type_initiative = new NYBCV_Post_Type_Initiative();
