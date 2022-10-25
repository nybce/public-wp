<?php
/**
 * Custom post type for Initiatives
 *
 */

/**
 * Class for the initiative post type.
 */
class MOCEJ_Post_Type_Initiative extends MOCEJ_Post_Type {

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
					'name'                     => __( 'Initiatives', 'mocej' ),
					'singular_name'            => __( 'Initiative', 'mocej' ),
					'add_new'                  => __( 'Add New Initiative', 'mocej' ),
					'add_new_item'             => __( 'Add New Initiative', 'mocej' ),
					'edit_item'                => __( 'Edit Initiative', 'mocej' ),
					'new_item'                 => __( 'New Initiative', 'mocej' ),
					'view_item'                => __( 'View Initiative', 'mocej' ),
					'view_items'               => __( 'View Initiatives', 'mocej' ),
					'search_items'             => __( 'Search Initiatives', 'mocej' ),
					'not_found'                => __( 'No initiatives found', 'mocej' ),
					'not_found_in_trash'       => __( 'No initiatives found in Trash', 'mocej' ),
					'parent_item_colon'        => __( 'Parent Initiative:', 'mocej' ),
					'all_items'                => __( 'All Initiatives', 'mocej' ),
					'archives'                 => __( 'Initiative Archives', 'mocej' ),
					'attributes'               => __( 'Initiative Attributes', 'mocej' ),
					'insert_into_item'         => __( 'Insert into initiative', 'mocej' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this initiative', 'mocej' ),
					'featured_image'           => __( 'Featured Image', 'mocej' ),
					'set_featured_image'       => __( 'Set featured image', 'mocej' ),
					'remove_featured_image'    => __( 'Remove featured image', 'mocej' ),
					'use_featured_image'       => __( 'Use as featured image', 'mocej' ),
					'filter_items_list'        => __( 'Filter initiatives list', 'mocej' ),
					'items_list_navigation'    => __( 'Initiatives list navigation', 'mocej' ),
					'items_list'               => __( 'Initiatives list', 'mocej' ),
					'item_published'           => __( 'Initiative published.', 'mocej' ),
					'item_published_privately' => __( 'Initiative published privately.', 'mocej' ),
					'item_reverted_to_draft'   => __( 'Initiative reverted to draft.', 'mocej' ),
					'item_scheduled'           => __( 'Initiative scheduled.', 'mocej' ),
					'item_updated'             => __( 'Initiative updated.', 'mocej' ),
					'menu_name'                => __( 'Initiatives', 'mocej' ),
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
$mocej_post_type_initiative = new MOCEJ_Post_Type_Initiative();
