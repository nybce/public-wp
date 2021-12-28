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
			add_filter( 'acf/register_block_type_args', array( 'NYBC_Blocks', 'register_block_type_args' ), 100, 1 );

			add_filter( 'block_categories_all', array( 'NYBC_Blocks', 'block_categories_all' ), 10, 2 );

			add_action( 'acf/init', array( 'NYBC_Blocks', 'init_block_types' ) );

			add_filter( 'allowed_block_types_all', array( 'NYBC_Blocks', 'allowed_block_types_all' ), 100, 2 );

			add_action( 'admin_head', array( 'NYBC_Blocks', 'admin_head' ) );

			add_filter( 'render_block', array( 'NYBC_Blocks', 'render_block' ), 100, 2 );

		}

		/**
		 *  Modify block args
		 *
		 * @param array $block block data.
		 *
		 * @return array
		 */
		public static function register_block_type_args( $block ) {
			$block['mode']     = 'edit';
			$block['category'] = 'nybc';

			if ( empty( $block['supports'] ) || $block['supports']['mode'] ) {
				$block['supports'] = array(
					'multiple' => true,
					'align'    => false,
					'mode'     => false,
				);
			}

			return $block;
		}

		/**
		 *  Modify standard blocks render
		 *
		 * @param string $block_content block content.
		 * @param array  $block block data.
		 */
		public static function render_block( $block_content, $block ) {
			if (
				in_array(
					$block['blockName'],
					array(
						'core/heading',
						'core/list',
						'core/quote',
						'core/paragraph',
					),
					true
				) && ! is_admin() && ! wp_is_json_request()
			) {
				$block_content = "<div class=\"text\">$block_content</div>";
			}

			return $block_content;
		}


		/**
		 *  Insert admin block styles
		 */
		public static function admin_head() {
			global $pagenow;
			if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) {
				return;
			}
			?>
			<style>
				.wp-block-acf-column-sidebar, .wp-block-acf-column-content, .wp-block-acf-two-column-block, .wp-block-acf-one-column-block, .wp-block-acf-accordion-item, .wp-block-acf-accordion{
					outline: 1px solid #c9c9c9;
					min-height: 40px;
				}
				.wp-block-acf-two-column-block .wp-block-acf-column-sidebar{
					width: 32%;
					display: inline-block;
					margin: 5px!important;
				}
				.wp-block-acf-two-column-block .wp-block-acf-column-content{
					width: 65%;
					display: inline-block;
					margin: 5px!important;
				}
				.wp-block-acf-two-column-block, .wp-block-acf-one-column-block, .wp-block-acf-accordion, .wp-block-acf-column-content, .wp-block-acf-column-sidebar {
					padding: 1px 10px!important;
				}

			</style>
			<?php
		}

		/**
		 *  Register allowed block types
		 */
		public static function allowed_block_types_all() {
			return array(
				'core/heading',
				'core/list',
				'core/quote',
				'core/paragraph',
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
				'acf/large-card',
				'acf/recent-news-feed',
				'acf/parent-page-hero',
				'acf/small-card-row',
				'acf/horizontal-cta-card',
				'acf/column-sidebar',
				'acf/column-content',
				'acf/two-column-block',
				'acf/one-column-block',
				'acf/zip-code-search',
				'acf/inline-video',
				'acf/inline-image',
				'acf/accordion-item',
				'acf/accordion',
				'acf/siderail-promo-cta',
				'acf/vertical-card-row',
				'acf/vertical-cta-card',
				'acf/spacer',
				'acf/child-page-hero',
				'acf/article-byline',
				'acf/download-card',
				'acf/download-card-container',
				'acf/graphic-download-card',
			);

		}

		/**
		 *  Register NYBC blocks category
		 *
		 * @param array  $block_categories block categories.
		 * @param object $editor_context editor context.
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
			self::two_column_block();// N2RDEV-80.
			self::one_column_block();// N2RDEV-80.
			self::spacer();
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
			self::large_card();// N2RDEV-48  #010.
			self::recent_news_feed();// N2RDEV-47.
			self::parent_page_hero();// N2RDEV-76.
			self::small_card_row();// N2RDEV-78.
			self::horizontal_cta_card();// N2RDEV-79.
			self::column_sidebar();// N2RDEV-80.
			self::column_content();// N2RDEV-80.
			self::zip_code_search();// N2RDEV-83.
			self::inline_video();// N2RDEV-85.
			self::inline_image();// N2RDEV-90.
			self::accordion_item();// N2RDEV-86.
			self::accordion();// N2RDEV-86.
			self::siderail_promo_cta();// N2RDEV-89.
			self::vertical_cta_card();// N2RDEV-87.
			self::vertical_card_row();// N2RDEV-87.
			self::child_page_hero();// N2RDEV-93.
			self::article_byline();// N2RDEV-97.
			self::download_card();// N2RDEV-125.
			self::download_card_container();// N2RDEV-126.
			self::graphic_download_card();// N2RDEV-127.
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
						'supports'        => array(
							'multiple' => false,
							'align'    => false,
							'mode'     => false,
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
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/employee-spotlight-carousel' );
			}
		}

		/**
		 *  Register Large Card block, N2RDEV-48  #010
		 */
		public static function large_card() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'large_card',
						'title'           => esc_html__( 'Large Card', 'nybc' ),
						'description'     => esc_html__( 'Large Card block', 'nybc' ),
						'render_template' => 'template-parts/blocks/large-card.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/large-card' );
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
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/recent-news-feed' );
			}
		}

		/**
		 *  Register Parent Page Hero block, N2RDEV-76
		 */
		public static function parent_page_hero() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'parent_page_hero',
						'title'           => esc_html__( 'Parent Page Hero', 'nybc' ),
						'description'     => esc_html__( 'Parent Page Hero block', 'nybc' ),
						'render_template' => 'template-parts/blocks/parent-page-hero.php',
						'supports'        => array(
							'multiple' => false,
							'align'    => false,
							'mode'     => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/parent-page-hero' );
			}
		}

		/**
		 *  Register Small Card Row block, N2RDEV-78
		 */
		public static function small_card_row() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'small_card_row',
						'title'           => esc_html__( 'Small Card Row', 'nybc' ),
						'description'     => esc_html__( 'Small Card Row block', 'nybc' ),
						'render_template' => 'template-parts/blocks/small-card-row.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/small-card-row' );
			}
		}

		/**
		 *  Register Horizontal CTA Card block, N2RDEV-79
		 */
		public static function horizontal_cta_card() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'horizontal_cta_card',
						'title'           => esc_html__( 'Horizontal CTA Card', 'nybc' ),
						'description'     => esc_html__( 'Horizontal CTA Card block', 'nybc' ),
						'render_template' => 'template-parts/blocks/horizontal-cta-card.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/horizontal-cta-card' );
			}
		}

		/**
		 *  Register Column Block, N2RDEV-80
		 */
		public static function column_sidebar() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'column_sidebar',
						'title'           => esc_html__( 'Column Sidebar', 'nybc' ),
						'description'     => esc_html__( 'Column Sidebar Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/column-sidebar.php',
						'parent'          => array( 'acf/two-column-block' ),
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
							'mode'     => false,
							'jsx'      => true,
						),
					)
				);
			}
		}

		/**
		 *  Register Column Block, N2RDEV-80
		 */
		public static function column_content() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'column_content',
						'title'           => esc_html__( 'Column Content', 'nybc' ),
						'description'     => esc_html__( 'Column Content Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/column-content.php',
						'parent'          => array( 'acf/two-column-block' ),
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
							'mode'     => false,
							'jsx'      => true,
						),
					)
				);
			}
		}

		/**
		 *  Register Two Column Block, N2RDEV-80
		 */
		public static function two_column_block() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'two_column_block',
						'title'           => esc_html__( 'Two Column Block', 'nybc' ),
						'description'     => esc_html__( 'Two Column Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/two-column-block.php',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
							'mode'     => false,
							'jsx'      => true,
						),
					)
				);
			}
		}

		/**
		 *  Register One Column Block, N2RDEV-80
		 */
		public static function one_column_block() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'one_column_block',
						'title'           => esc_html__( 'One Column Block', 'nybc' ),
						'description'     => esc_html__( 'One Column Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/one-column-block.php',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
							'mode'     => false,
							'jsx'      => true,
						),
					)
				);
			}
		}

		/**
		 *  Zip Code Search Block, N2RDEV-83
		 */
		public static function zip_code_search() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'zip_code_search',
						'title'           => esc_html__( 'Zip Code Search', 'nybc' ),
						'description'     => esc_html__( 'Zip Code Search Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/zip-code-search.php',
						'parent'          => array( 'acf/column-sidebar' ),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/zip-code-search' );
			}
		}

		/**
		 *  Inline Video with Caption Block, N2RDEV-85
		 */
		public static function inline_video() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'inline_video',
						'title'           => esc_html__( 'Inline Video with Caption', 'nybc' ),
						'description'     => esc_html__( 'Inline Video with Caption Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/inline-video.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/inline-video' );
			}
		}

		/**
		 *  Inline Image with Caption Block, N2RDEV-90
		 */
		public static function inline_image() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'inline_image',
						'title'           => esc_html__( 'Inline Image with Caption', 'nybc' ),
						'description'     => esc_html__( 'Inline Image with Caption Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/inline-image.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/inline-image' );
			}
		}


		/**
		 *  Accordion Block, N2RDEV-86
		 */
		public static function accordion() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'accordion',
						'title'           => esc_html__( 'Accordion', 'nybc' ),
						'description'     => esc_html__( 'Accordion Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/accordion.php',
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
							'mode'     => false,
							'jsx'      => true,
						),
					)
				);
			}
		}

		/**
		 *  Accordion Item, N2RDEV-86
		 */
		public static function accordion_item() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'accordion_item',
						'title'           => esc_html__( 'Accordion Item', 'nybc' ),
						'description'     => esc_html__( 'Accordion Item Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/accordion-item.php',
						'parent'          => array( 'acf/accordion' ),
						'supports'        => array(
							'multiple' => true,
							'align'    => false,
							'mode'     => false,
							'jsx'      => true,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/accordion-item' );
			}
		}

		/**
		 *  Siderail Promo CTA Block, N2RDEV-89
		 */
		public static function siderail_promo_cta() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'siderail_promo_cta',
						'title'           => esc_html__( 'Siderail Promo CTA', 'nybc' ),
						'description'     => esc_html__( 'Siderail Promo CTA Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/siderail-promo-cta.php',
						'parent'          => array( 'acf/column-sidebar' ),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/siderail-promo-cta' );
			}
		}

		/**
		 *  Register Vertical CTA Card block, N2RDEV-87
		 */
		public static function vertical_cta_card() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'vertical_cta_card',
						'title'           => esc_html__( 'Vertical CTA Card', 'nybc' ),
						'description'     => esc_html__( 'Vertical CTA Card block', 'nybc' ),
						'render_template' => 'template-parts/blocks/vertical-cta-card.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/vertical-cta-card' );
			}
		}

		/**
		 *  Register Vertical Card Row block, N2RDEV-87
		 */
		public static function vertical_card_row() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'vertical_card_row',
						'title'           => esc_html__( 'Vertical Card Row', 'nybc' ),
						'description'     => esc_html__( 'Vertical Card Row block', 'nybc' ),
						'render_template' => 'template-parts/blocks/vertical-card-row.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/vertical-card-row' );
			}
		}

		/**
		 *  Register Spacer block
		 */
		public static function spacer() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'spacer',
						'title'           => esc_html__( 'Spacer', 'nybc' ),
						'description'     => esc_html__( 'Spacer block', 'nybc' ),
						'render_template' => 'template-parts/blocks/spacer.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/spacer' );
			}
		}

		/**
		 *  Register Child Page Hero block, N2RDEV-20, #0110
		 */
		public static function child_page_hero() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'child_page_hero',
						'title'           => esc_html__( 'Child Page Hero', 'nybc' ),
						'description'     => esc_html__( 'Child Page Hero block', 'nybc' ),
						'render_template' => 'template-parts/blocks/child-page-hero.php',
						'supports'        => array(
							'multiple' => false,
							'align'    => false,
							'mode'     => false,
						),
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/child-page-hero' );
			}
		}

		/**
		 *  Article Byline Block, N2RDEV-97
		 */
		public static function article_byline() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'article_byline',
						'title'           => esc_html__( 'Article Byline', 'nybc' ),
						'description'     => esc_html__( 'Article Byline Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/article-byline.php',
					)
				);
				
				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/article-byline' );
			}
		}

		/**
		 *  Download Card Block, N2RDEV-125
		 */
		public static function download_card() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'download_card',
						'title'           => esc_html__( 'Download Card', 'nybc' ),
						'description'     => esc_html__( 'Download Card Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/download-card.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/download-card' );
			}
		}

		/**
		 *  Download Card Container Block, N2RDEV-126
		 */
		public static function download_card_container() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'download_card_container',
						'title'           => esc_html__( 'Download Card Container', 'nybc' ),
						'description'     => esc_html__( 'Download Card Container Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/download-card-container.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/download-card-container' );
			}
		}

		/**
		 *  Graphic Download Card Block, N2RDEV-127
		 */
		public static function graphic_download_card() {
			if ( function_exists( 'acf_register_block_type' ) ) {
				acf_register_block_type(
					array(
						'name'            => 'graphic_download_card',
						'title'           => esc_html__( 'Graphic Download Card', 'nybc' ),
						'description'     => esc_html__( 'Graphic Download Card Block', 'nybc' ),
						'render_template' => 'template-parts/blocks/graphic-download-card.php',
					)
				);

				/**
				 *  Add block fields
				 */
				get_template_part( 'inc/acf/blocks/graphic-download-card' );
			}
		}

	}

	new NYBC_Blocks();
}
