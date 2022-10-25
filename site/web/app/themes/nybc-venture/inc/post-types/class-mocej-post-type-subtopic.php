<?php
/**
 * Custom post type for Subtopics
 *
 */

/**
 * Class for the subtopic post type.
 */
class MOCEJ_Post_Type_Subtopic extends MOCEJ_Post_Type {

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
					'name'                     => __( 'Subtopics', 'mocej' ),
					'singular_name'            => __( 'Subtopic', 'mocej' ),
					'add_new'                  => __( 'Add New Subtopic', 'mocej' ),
					'add_new_item'             => __( 'Add New Subtopic', 'mocej' ),
					'edit_item'                => __( 'Edit Subtopic', 'mocej' ),
					'new_item'                 => __( 'New Subtopic', 'mocej' ),
					'view_item'                => __( 'View Subtopic', 'mocej' ),
					'view_items'               => __( 'View Subtopics', 'mocej' ),
					'search_items'             => __( 'Search Subtopics', 'mocej' ),
					'not_found'                => __( 'No subtopics found', 'mocej' ),
					'not_found_in_trash'       => __( 'No subtopics found in Trash', 'mocej' ),
					'parent_item_colon'        => __( 'Parent Subtopic:', 'mocej' ),
					'all_items'                => __( 'All Subtopics', 'mocej' ),
					'archives'                 => __( 'Subtopic Archives', 'mocej' ),
					'attributes'               => __( 'Subtopic Attributes', 'mocej' ),
					'insert_into_item'         => __( 'Insert into subtopic', 'mocej' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this subtopic', 'mocej' ),
					'featured_image'           => __( 'Featured Image', 'mocej' ),
					'set_featured_image'       => __( 'Set featured image', 'mocej' ),
					'remove_featured_image'    => __( 'Remove featured image', 'mocej' ),
					'use_featured_image'       => __( 'Use as featured image', 'mocej' ),
					'filter_items_list'        => __( 'Filter subtopics list', 'mocej' ),
					'items_list_navigation'    => __( 'Subtopics list navigation', 'mocej' ),
					'items_list'               => __( 'Subtopics list', 'mocej' ),
					'item_published'           => __( 'Subtopic published.', 'mocej' ),
					'item_published_privately' => __( 'Subtopic published privately.', 'mocej' ),
					'item_reverted_to_draft'   => __( 'Subtopic reverted to draft.', 'mocej' ),
					'item_scheduled'           => __( 'Subtopic scheduled.', 'mocej' ),
					'item_updated'             => __( 'Subtopic updated.', 'mocej' ),
					'menu_name'                => __( 'Subtopics', 'mocej' ),
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
$mocej_post_type_subtopic = new MOCEJ_Post_Type_Subtopic();
