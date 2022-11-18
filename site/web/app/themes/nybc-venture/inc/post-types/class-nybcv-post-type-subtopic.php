<?php
/**
 * Custom post type for Subtopics
 *
 */

/**
 * Class for the subtopic post type.
 */
class NYBCV_Post_Type_Subtopic extends NYBCV_Post_Type {

	/**
	 * Name of the custom post type.
	 *
	 * @var string
	 */
	public $name = 'subtopic';

	/**
	 * Creates the post type.
	 */
	public function create_post_type() {
		register_post_type(
			$this->name,
			[
				'labels' => [
					'name'                     => __( 'Subtopics', 'nybcv' ),
					'singular_name'            => __( 'Subtopic', 'nybcv' ),
					'add_new'                  => __( 'Add New Subtopic', 'nybcv' ),
					'add_new_item'             => __( 'Add New Subtopic', 'nybcv' ),
					'edit_item'                => __( 'Edit Subtopic', 'nybcv' ),
					'new_item'                 => __( 'New Subtopic', 'nybcv' ),
					'view_item'                => __( 'View Subtopic', 'nybcv' ),
					'view_items'               => __( 'View Subtopics', 'nybcv' ),
					'search_items'             => __( 'Search Subtopics', 'nybcv' ),
					'not_found'                => __( 'No subtopics found', 'nybcv' ),
					'not_found_in_trash'       => __( 'No subtopics found in Trash', 'nybcv' ),
					'parent_item_colon'        => __( 'Parent Subtopic:', 'nybcv' ),
					'all_items'                => __( 'All Subtopics', 'nybcv' ),
					'archives'                 => __( 'Subtopic Archives', 'nybcv' ),
					'attributes'               => __( 'Subtopic Attributes', 'nybcv' ),
					'insert_into_item'         => __( 'Insert into subtopic', 'nybcv' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this subtopic', 'nybcv' ),
					'featured_image'           => __( 'Featured Image', 'nybcv' ),
					'set_featured_image'       => __( 'Set featured image', 'nybcv' ),
					'remove_featured_image'    => __( 'Remove featured image', 'nybcv' ),
					'use_featured_image'       => __( 'Use as featured image', 'nybcv' ),
					'filter_items_list'        => __( 'Filter subtopics list', 'nybcv' ),
					'items_list_navigation'    => __( 'Subtopics list navigation', 'nybcv' ),
					'items_list'               => __( 'Subtopics list', 'nybcv' ),
					'item_published'           => __( 'Subtopic published.', 'nybcv' ),
					'item_published_privately' => __( 'Subtopic published privately.', 'nybcv' ),
					'item_reverted_to_draft'   => __( 'Subtopic reverted to draft.', 'nybcv' ),
					'item_scheduled'           => __( 'Subtopic scheduled.', 'nybcv' ),
					'item_updated'             => __( 'Subtopic updated.', 'nybcv' ),
					'menu_name'                => __( 'Subtopics', 'nybcv' ),
				],
				'public' => true,
				'menu_icon' => 'dashicons-book',
				'rewrite' => [
					'slug' => 'subtopics',
				],
				'show_in_rest' => true,
				'supports' => [ 'title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields' ],
			]
		);
	}
}
$nybcv_post_type_subtopic = new NYBCV_Post_Type_Subtopic();
