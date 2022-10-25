<?php
/**
 * Rebuild Taxonomy - Topics
 *
 */

/**
 * Class for the topic taxonomy.
 */
class MOCEJ_Taxonomy_Topic extends MOCEJ_Taxonomy {

	/**
	 * Name of the taxonomy.
	 *
	 * @var string
	 */
	public $name = 'topic';

	/**
	 * Object types for this taxonomy.
	 *
	 * @var array
	 */
	public $object_types;


	/**
	 * Build the taxonomy object.
	 */
	public function __construct() {
		$this->object_types = ['post','page','initiative'];

		parent::__construct();
	}

	/**
	 * Creates the taxonomy.
	 */
	public function create_taxonomy() {
		register_taxonomy(
			$this->name,
			$this->object_types,
			[
				'labels' => [
					'name'                  => __( 'Topics', 'state-of-obesity' ),
					'singular_name'         => __( 'Topic', 'state-of-obesity' ),
					'search_items'          => __( 'Search Topics', 'state-of-obesity' ),
					'popular_items'         => __( 'Popular Topics', 'state-of-obesity' ),
					'all_items'             => __( 'All Topics', 'state-of-obesity' ),
					'parent_item'           => __( 'Parent Topic', 'state-of-obesity' ),
					'parent_item_colon'     => __( 'Parent Topic', 'state-of-obesity' ),
					'edit_item'             => __( 'Edit Topic', 'state-of-obesity' ),
					'view_item'             => __( 'View Topic', 'state-of-obesity' ),
					'update_item'           => __( 'Update Topic', 'state-of-obesity' ),
					'add_new_item'          => __( 'Add New Topic', 'state-of-obesity' ),
					'new_item_name'         => __( 'New Topic Name', 'state-of-obesity' ),
					'add_or_remove_items'   => __( 'Add or remove Topic', 'state-of-obesity' ),
					'choose_from_most_used' => __( 'Choose from the most used Topics', 'state-of-obesity' ),
					'not_found'             => __( 'No Topics found', 'state-of-obesity' ),
					'no_terms'              => __( 'No Topics', 'state-of-obesity' ),
					'items_list_navigation' => __( 'Topics list navigation', 'state-of-obesity' ),
					'items_list'            => __( 'Topics list', 'state-of-obesity' ),
					'back_to_items'         => __( '&larr; Back to Topics', 'state-of-obesity' ),
					'menu_name'             => __( 'Topics', 'state-of-obesity' ),
					'name_admin_bar'        => __( 'Topics', 'state-of-obesity' ),
				],
				'hierarchical' => true,
				'show_admin_column' => true,
				'show_in_rest' => true,
			]
		);
	}
}
$mocej_taxonomy_topic = new MOCEJ_Taxonomy_Topic();
