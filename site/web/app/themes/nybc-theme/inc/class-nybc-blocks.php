<?php
/**
 * NYBC Blocks Init
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
				'acf/promo-home-cta',
				'acf/full-width-feature-cta',
				'acf/full-width-feature-cta-carousel',
				'acf/tabbed-card-carousel',
				'acf/callout-with-cta-carousel',
				'acf/callout-with-cta',
				'acf/featured-content-feed',
				'acf/small-card',
				'acf/featured-content-card',
				'acf/full-width-pullquote',
				'acf/employee-spotlight-carousel',
				'acf/recent-news-feed',
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
			self::home_hero(); // N2RDEV-20   #0110.
			self::promo_home_cta();// N2RDEV-21  #0210.
			self::full_width_feature_cta();// N2RDEV-22 #0230.
			self::full_width_feature_cta_carousel();// N2RDEV-23  #0240.
			self::tabbed_card_carousel();// N2RDEV-38  #0250.
			self::callout_with_cta();// N2RDEV-39   #0270.
			self::callout_with_cta_carousel();// N2RDEV-40  #0260.
			self::featured_content_feed();// N2RDEV-41  #02110.
			self::small_card();// N2RDEV-43  #011.
			self::featured_content_card();// N2RDEV-44 #019.
			self::full_width_pullquote();// N2RDEV-44 #019.
			self::employee_spotlight_carousel();// N2RDEV-46  #0290.
			self::recent_news_feed();// N2RDEV-47.
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

		/**
		 *  Register Promo Home CTA block, N2RDEV-21  #0210
		 */
		public static function promo_home_cta() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'promo_home_cta',
						'title'           => esc_html__( 'Promo Home CTA', 'nybc' ),
						'description'     => esc_html__( 'Promo Home CTA for Home Page', 'nybc' ),
						'render_template' => 'template-parts/blocks/promo-home-cta.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/promo-home-cta' );
			}
		}

		/**
		 *  Register Full Width Feature CTA block, N2RDEV-22 #0230
		 */
		public static function full_width_feature_cta() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'full_width_feature_cta',
						'title'           => esc_html__( 'Full Width Feature CTA', 'nybc' ),
						'description'     => esc_html__( 'Full Width Feature CTA block', 'nybc' ),
						'render_template' => 'template-parts/blocks/full-width-feature-cta.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/full-width-feature-cta' );
			}
		}

		/**
		 *  Register Full Width Feature CTA Carousel block, N2RDEV-23  #0240
		 */
		public static function full_width_feature_cta_carousel() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'full_width_feature_cta_carousel',
						'title'           => esc_html__( 'Full Width Feature CTA Carousel', 'nybc' ),
						'description'     => esc_html__( 'Full Width Feature CTA Carousel block', 'nybc' ),
						'render_template' => 'template-parts/blocks/full-width-feature-cta-carousel.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/full-width-feature-cta-carousel' );
			}
		}

		/**
		 *  Register Tabbed Card Carousel block, N2RDEV-38  #0250
		 */
		public static function tabbed_card_carousel() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'tabbed_card_carousel',
						'title'           => esc_html__( 'Tabbed Card Carousel', 'nybc' ),
						'description'     => esc_html__( 'Tabbed Card Carousel block', 'nybc' ),
						'render_template' => 'template-parts/blocks/tabbed-card-carousel.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/tabbed-card-carousel' );
			}
		}

		/**
		 *  Register Carousel - Callout with CTA block, N2RDEV-40  #0260
		 */
		public static function callout_with_cta_carousel() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'callout_with_cta_carousel',
						'title'           => esc_html__( 'Callout with CTA Carousel', 'nybc' ),
						'description'     => esc_html__( 'Callout with CTA Carousel block', 'nybc' ),
						'render_template' => 'template-parts/blocks/callout-with-cta-carousel.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/callout-with-cta-carousel' );
			}
		}

		/**
		 *  Register Callout with CTA block, N2RDEV-39  #0270 //TODO
		 */
		public static function callout_with_cta() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'callout_with_cta',
						'title'           => esc_html__( 'Callout with CTA', 'nybc' ),
						'description'     => esc_html__( 'Callout with CTA block', 'nybc' ),
						'render_template' => 'template-parts/blocks/callout-with-cta.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/callout-with-cta' );
			}
		}

		/**
		 *  Register Featured Content Feed block, N2RDEV-41  #02110
		 */
		public static function featured_content_feed() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'featured_content_feed',
						'title'           => esc_html__( 'Featured Content Feed', 'nybc' ),
						'description'     => esc_html__( 'Featured Content Feed block', 'nybc' ),
						'render_template' => 'template-parts/blocks/featured-content-feed.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/featured-content-feed' );
			}
		}

		/**
		 *  Register Featured Content Card block, N2RDEV-44  #019
		 */
		public static function featured_content_card() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'featured_content_card',
						'title'           => esc_html__( 'Featured Content Card', 'nybc' ),
						'description'     => esc_html__( 'Featured Content Card block', 'nybc' ),
						'render_template' => 'template-parts/blocks/featured-content-card.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/featured-content-card' );
			}
		}

		/**
		 *  Register Small Card block, N2RDEV-43  #011
		 */
		public static function small_card() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'small_card',
						'title'           => esc_html__( 'Small Card', 'nybc' ),
						'description'     => esc_html__( 'Small Card block', 'nybc' ),
						'render_template' => 'template-parts/blocks/small-card.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/small-card' );
			}
		}

		/**
		 *  Register Full Width Pullquote block, N2RDEV-45  #0300
		 */
		public static function full_width_pullquote() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'full_width_pullquote',
						'title'           => esc_html__( 'Full Width Pullquote', 'nybc' ),
						'description'     => esc_html__( 'Full Width Pullquote block', 'nybc' ),
						'render_template' => 'template-parts/blocks/full-width-pullquote.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/full-width-pullquote' );
			}
		}

		/**
		 *  Register Employee Spotlight Carousel block, N2RDEV-46  #0290
		 */
		public static function employee_spotlight_carousel() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'employee_spotlight_carousel',
						'title'           => esc_html__( 'Employee Spotlight Carousel', 'nybc' ),
						'description'     => esc_html__( 'Employee Spotlight Carousel block', 'nybc' ),
						'render_template' => 'template-parts/blocks/employee-spotlight-carousel.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/employee-spotlight-carousel' );
			}
		}

		/**
		 *  Register Recent News Feed block, N2RDEV-47
		 */
		public static function recent_news_feed() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'recent_news_feed',
						'title'           => esc_html__( 'Recent News Feed', 'nybc' ),
						'description'     => esc_html__( 'Recent News Feed block', 'nybc' ),
						'render_template' => 'template-parts/blocks/recent-news-feed.php',
						'category'        => 'nybc',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/recent-news-feed' );
			}
		}
	}


	new NYBC_Blocks();
}
