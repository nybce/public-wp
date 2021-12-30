<?php
/**
 * NYBC Block Patterns Init
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Block_Patterns' ) ) {
	/**
	 * NYBC Block Patterns Init class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Block_Patterns {


		/**
		 *  NYBC_Block_Patterns constructor
		 */
		public function __construct() {
			if ( function_exists( 'register_block_pattern_category' ) ) {

				register_block_pattern_category(
					'nybc',
					array( 'label' => esc_html__( 'NYBC Patterns', 'nybc' ) )
				);

			}

			self::register_block_patterns();
		}

		/**
		 *  Register all block patterns
		 */
		public static function register_block_patterns() {
			if ( function_exists( 'register_block_pattern' ) ) {

				register_block_pattern(
					'nybc/parent-page',
					array(
						'title'      => esc_html__( 'Parent Page', 'nybc' ),
						'categories' => array( 'nybc' ),
						'content'    => '
<!-- wp:acf/parent-page-hero {"id":"block_61c1b98fcf25b","name":"acf/parent-page-hero","data":{"field_61b87fc33dc60":"This is a top-level parent page. The message here should be imactful and succint.","field_61b880133dc61":"492","field_61b8804f3dc62":{"title":"Optional Call-to-Action Button","url":"#","target":""}},"align":"","mode":"edit"} /-->

<!-- wp:acf/two-column-block {"id":"block_61c1b95bcf258","name":"acf/two-column-block","align":"","mode":"preview"} -->
<!-- wp:acf/column-sidebar {"id":"block_61c1b95ecf259","name":"acf/column-sidebar","data":{},"align":"","mode":"preview"} /-->

<!-- wp:acf/column-content {"id":"block_61c1b95ecf25a","name":"acf/column-content","data":{},"align":"","mode":"preview"} /-->
<!-- /wp:acf/two-column-block -->',
					)
				);

			}
		}
	}

	new NYBC_Block_Patterns();
}
