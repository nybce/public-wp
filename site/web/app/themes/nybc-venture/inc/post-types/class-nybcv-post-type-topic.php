<?php
/**
 * Custom post type for Topics
 *
 */

/**
 * Class for the topic post type.
 */
class NYBCV_Post_Type_Topic extends NYBCV_Post_Type {

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
					'name'                     => __( 'Topics', 'nybcv' ),
					'singular_name'            => __( 'Topic', 'nybcv' ),
					'add_new'                  => __( 'Add New Topic', 'nybcv' ),
					'add_new_item'             => __( 'Add New Topic', 'nybcv' ),
					'edit_item'                => __( 'Edit Topic', 'nybcv' ),
					'new_item'                 => __( 'New Topic', 'nybcv' ),
					'view_item'                => __( 'View Topic', 'nybcv' ),
					'view_items'               => __( 'View Topics', 'nybcv' ),
					'search_items'             => __( 'Search Topics', 'nybcv' ),
					'not_found'                => __( 'No topics found', 'nybcv' ),
					'not_found_in_trash'       => __( 'No topics found in Trash', 'nybcv' ),
					'parent_item_colon'        => __( 'Parent Topic:', 'nybcv' ),
					'all_items'                => __( 'All Topics', 'nybcv' ),
					'archives'                 => __( 'Topic Archives', 'nybcv' ),
					'attributes'               => __( 'Topic Attributes', 'nybcv' ),
					'insert_into_item'         => __( 'Insert into topic', 'nybcv' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this topic', 'nybcv' ),
					'featured_image'           => __( 'Featured Image', 'nybcv' ),
					'set_featured_image'       => __( 'Set featured image', 'nybcv' ),
					'remove_featured_image'    => __( 'Remove featured image', 'nybcv' ),
					'use_featured_image'       => __( 'Use as featured image', 'nybcv' ),
					'filter_items_list'        => __( 'Filter topics list', 'nybcv' ),
					'items_list_navigation'    => __( 'Topics list navigation', 'nybcv' ),
					'items_list'               => __( 'Topics list', 'nybcv' ),
					'item_published'           => __( 'Topic published.', 'nybcv' ),
					'item_published_privately' => __( 'Topic published privately.', 'nybcv' ),
					'item_reverted_to_draft'   => __( 'Topic reverted to draft.', 'nybcv' ),
					'item_scheduled'           => __( 'Topic scheduled.', 'nybcv' ),
					'item_updated'             => __( 'Topic updated.', 'nybcv' ),
					'menu_name'                => __( 'Topics', 'nybcv' ),
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
$nybcv_post_type_topic = new NYBCV_Post_Type_Topic();
