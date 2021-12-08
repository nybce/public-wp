<?php
/**
 * NYBC Blocks Init YBC Blocks Init YBC Blocks Init
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Blocks' ) ) {
	/**
	 * NYBC Blocks Init class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Blocks {



		/**
		 *  NYBC_Blocks constructor
		 */
		public function __construct() {
			add_filter( 'block_categories_all', array( 'NYBC_Blocks', 'block_categories_all' ), 10, 2 );

			add_action( 'acf/init', array( 'NYBC_Blocks', 'init_block_types' ) );

			add_filter( 'allowed_block_types_all', array( 'NYBC_Blocks', 'allowed_block_types_all' ), 100, 2 );

		}

		/**
		 *  Register allowed block types
		 */
		public static function allowed_block_types_all() {
			return array(
				'acf/home-hero',
			);

		}

		/**
		 *  Register NYBC blocks category
		 *
		 * @param array  $block_categories  block categories.
		 * @param object $editor_context    editor context.
		 */
		public static function block_categories_all( $block_categories, $editor_context ) {
			if ( ! empty( $editor_context->post ) ) {
				array_push(
					$block_categories,
					array(
						'slug'  => 'nybc',
						'title' => esc_html__( 'NYBC', 'nybc' ),
						'icon'  => null,
					)
				);
			}
			return $block_categories;
		}

		/**
		 *  Init NYBC blocks
		 */
		public static function init_block_types() {
			self::home_hero();
		}

		/**
		 *  Register Home Hero block, N2RDEV-20, #0110
		 */
		public static function home_hero() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'home_hero',
						'title'           => esc_html__( 'Home Hero', 'nybc' ),
						'description'     => esc_html__( 'Home Hero for Home Page', 'nybc' ),
						'render_template' => 'template-parts/blocks/home-hero.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => false,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/home-hero' );
			}
		}

	}

	new NYBC_Blocks();
}
