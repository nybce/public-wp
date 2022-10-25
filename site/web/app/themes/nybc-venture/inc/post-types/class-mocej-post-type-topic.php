<?php
/**
 * Custom post type for Topics
 *
 */

/**
 * Class for the topic post type.
 */
class MOCEJ_Post_Type_Topic extends MOCEJ_Post_Type {

	/**
	 * Name of the custom post type.
	 *
	 * @var string
	 */
	public $name = 'topic';

	/**
	 * Creates the post type.
	 */
	public function create_post_type() {
		register_post_type(
			$this->name,
			[
				'labels' => [
					'name'                     => __( 'Topics', 'mocej' ),
					'singular_name'            => __( 'Topic', 'mocej' ),
					'add_new'                  => __( 'Add New Topic', 'mocej' ),
					'add_new_item'             => __( 'Add New Topic', 'mocej' ),
					'edit_item'                => __( 'Edit Topic', 'mocej' ),
					'new_item'                 => __( 'New Topic', 'mocej' ),
					'view_item'                => __( 'View Topic', 'mocej' ),
					'view_items'               => __( 'View Topics', 'mocej' ),
					'search_items'             => __( 'Search Topics', 'mocej' ),
					'not_found'                => __( 'No topics found', 'mocej' ),
					'not_found_in_trash'       => __( 'No topics found in Trash', 'mocej' ),
					'parent_item_colon'        => __( 'Parent Topic:', 'mocej' ),
					'all_items'                => __( 'All Topics', 'mocej' ),
					'archives'                 => __( 'Topic Archives', 'mocej' ),
					'attributes'               => __( 'Topic Attributes', 'mocej' ),
					'insert_into_item'         => __( 'Insert into topic', 'mocej' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this topic', 'mocej' ),
					'featured_image'           => __( 'Featured Image', 'mocej' ),
					'set_featured_image'       => __( 'Set featured image', 'mocej' ),
					'remove_featured_image'    => __( 'Remove featured image', 'mocej' ),
					'use_featured_image'       => __( 'Use as featured image', 'mocej' ),
					'filter_items_list'        => __( 'Filter topics list', 'mocej' ),
					'items_list_navigation'    => __( 'Topics list navigation', 'mocej' ),
					'items_list'               => __( 'Topics list', 'mocej' ),
					'item_published'           => __( 'Topic published.', 'mocej' ),
					'item_published_privately' => __( 'Topic published privately.', 'mocej' ),
					'item_reverted_to_draft'   => __( 'Topic reverted to draft.', 'mocej' ),
					'item_scheduled'           => __( 'Topic scheduled.', 'mocej' ),
					'item_updated'             => __( 'Topic updated.', 'mocej' ),
					'menu_name'                => __( 'Topics', 'mocej' ),
				],
				'public' => true,
				'menu_icon' => 'dashicons-book',
				'rewrite' => [
					'slug' => 'topics',
				],
				'show_in_rest' => true,
				'supports' => [ 'title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields' ],
			]
		);
	}
}
$mocej_post_type_topic = new MOCEJ_Post_Type_Topic();
