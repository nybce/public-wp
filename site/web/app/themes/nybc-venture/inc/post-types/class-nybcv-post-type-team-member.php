<?php
/**
 * Custom post type for Team Member
 *
 */

/**
 * Class for the team member post type.
 */
class NYBCV_Post_Type_Team_Member extends NYBCV_Post_Type {

	/**
	 * Name of the custom post type.
	 *
	 * @var string
	 */
	public $name = 'team-member';

	/**
	 * Creates the post type.
	 */
	public function create_post_type() {
		register_post_type(
			$this->name,
			[
				'labels' => [
					'name'                     => __( 'Team Members', 'nybcv' ),
					'singular_name'            => __( 'Team Member', 'nybcv' ),
					'add_new'                  => __( 'Add New Team Member', 'nybcv' ),
					'add_new_item'             => __( 'Add New Team Member', 'nybcv' ),
					'edit_item'                => __( 'Edit Team Member', 'nybcv' ),
					'new_item'                 => __( 'New Team Member', 'nybcv' ),
					'view_item'                => __( 'View Team Member', 'nybcv' ),
					'view_items'               => __( 'View Team Members', 'nybcv' ),
					'search_items'             => __( 'Search Team Members', 'nybcv' ),
					'not_found'                => __( 'No team members found', 'nybcv' ),
					'not_found_in_trash'       => __( 'No team members found in Trash', 'nybcv' ),
					'parent_item_colon'        => __( 'Parent Team Member:', 'nybcv' ),
					'all_items'                => __( 'All Team Members', 'nybcv' ),
					'archives'                 => __( 'Team Member Archives', 'nybcv' ),
					'attributes'               => __( 'Team Member Attributes', 'nybcv' ),
					'insert_into_item'         => __( 'Insert into team member', 'nybcv' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this team member', 'nybcv' ),
					'featured_image'           => __( 'Featured Image', 'nybcv' ),
					'set_featured_image'       => __( 'Set featured image', 'nybcv' ),
					'remove_featured_image'    => __( 'Remove featured image', 'nybcv' ),
					'use_featured_image'       => __( 'Use as featured image', 'nybcv' ),
					'filter_items_list'        => __( 'Filter team members list', 'nybcv' ),
					'items_list_navigation'    => __( 'Team Members list navigation', 'nybcv' ),
					'items_list'               => __( 'Team Members list', 'nybcv' ),
					'item_published'           => __( 'Team Member published.', 'nybcv' ),
					'item_published_privately' => __( 'Team Member published privately.', 'nybcv' ),
					'item_reverted_to_draft'   => __( 'Team Member reverted to draft.', 'nybcv' ),
					'item_scheduled'           => __( 'Team Member scheduled.', 'nybcv' ),
					'item_updated'             => __( 'Team Member updated.', 'nybcv' ),
					'menu_name'                => __( 'Team Members', 'nybcv' ),
				],
				'public' => true,
				'menu_icon' => 'dashicons-book',
				'rewrite' => [
					'slug' => 'team-member',
				],
				'show_in_rest' => true,
				'supports' => [ 'title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields' ],
			]
		);
	}
}
$nybcv_post_type_team_member = new NYBCV_Post_Type_Team_Member();
