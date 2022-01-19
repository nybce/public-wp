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

			if ( function_exists( 'register_block_pattern_category' ) ) {

				/**
				 *  Parent Page pattern
				 */
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

				/**
				 *  Child Page Two Column pattern
				 */
				register_block_pattern(
					'nybc/child-page-two-column',
					array(
						'title'      => esc_html__( 'Child Page Two Column', 'nybc' ),
						'categories' => array( 'nybc' ),
						'content'    => '
<!-- wp:acf/child-page-hero {"id":"block_61d2bc1566487","name":"acf/child-page-hero","data":{"field_61bc7965d737f":"Optional Tagline","field_61bc7977d7380":"88","field_61bc79a8d7381":"#008599","field_61bc79ebd7382":{"title":"Optional Call-to-Action","url":"#","target":""}},"align":"","mode":"edit"} /-->
<!-- wp:acf/two-column-block {"id":"block_61d2bca266488","name":"acf/two-column-block","data":{"field_61cc812303ef5":"0"},"align":"","mode":"preview"} -->
<!-- wp:acf/column-sidebar {"id":"block_61d2bca266489","name":"acf/column-sidebar","align":"","mode":"preview"} /-->
<!-- wp:acf/column-content {"id":"block_61d2bca26648a","name":"acf/column-content","align":"","mode":"preview"} /-->
<!-- /wp:acf/two-column-block -->',
					)
				);

				/**
				 *  Child Page One Column pattern
				 */
				register_block_pattern(
					'nybc/child-page-one-column',
					array(
						'title'      => esc_html__( 'Child Page One Column', 'nybc' ),
						'categories' => array( 'nybc' ),
						'content'    => '
<!-- wp:acf/child-page-hero {"id":"block_61d2bc1566487","name":"acf/child-page-hero","data":{"field_61bc7965d737f":"Optional Tagline","field_61bc7977d7380":"88","field_61bc79a8d7381":"#008599","field_61bc79ebd7382":{"title":"Optional Call-to-Action","url":"#","target":""}},"align":"","mode":"edit"} /-->
<!-- wp:acf/one-column-block {"id":"block_61d2bdf56648b","name":"acf/one-column-block","data":{"field_61cc812303ef5":"0"},"align":"","mode":"preview"} -->
<!-- wp:acf/column-content {"id":"block_61d2bdf66648c","name":"acf/column-content","align":"","mode":"preview"} /-->
<!-- /wp:acf/one-column-block -->',
					)
				);

				/**
				 *  News Landing pattern
				 */
				register_block_pattern(
					'nybc/news-landing',
					array(
						'title'      => esc_html__( 'News Landing', 'nybc' ),
						'categories' => array( 'nybc' ),
						'content'    => '
<!-- wp:acf/child-page-hero {"id":"block_61cc78f01b2ab","name":"acf/child-page-hero","data":{"tagline":"Optional Tagline","_tagline":"field_61bc7965d737f","image":88,"_image":"field_61bc7977d7380","color":"#008599","_color":"field_61bc79a8d7381","link":{"title":"Optional Call-to-Action","url":"#","target":""},"_link":"field_61bc79ebd7382"},"align":"","mode":"edit"} /-->
<!-- wp:acf/two-column-block {"id":"block_61cc79611b2ac","name":"acf/two-column-block","data":{"decor":"1","_decor":"field_61cc812303ef5"},"align":"","mode":"preview"} -->
<!-- wp:acf/column-sidebar {"id":"block_61cc79611b2ad","name":"acf/column-sidebar","align":"","mode":"preview"} -->
<!-- wp:acf/zip-code-search {"id":"block_61c9f6d8424f0","name":"acf/zip-code-search","data":{"title":"Search donation centers near you","_title":"field_61b9f5c272f4f","input_label":"Enter your ZIP","_input_label":"field_61b9f5f672f53","link":{"title":"Lookup","url":"#","target":"_blank"},"_link":"field_61b9f5f672f52","description_title":"Did You Know?","_description_title":"field_61b9f5f572f51","description":"One pint of blood can save as many as three lives. There are donor centers in your neighborhood and almost 50 blood drives a day in NY, NJ and beyond.","_description":"field_61b9f5f372f50"},"align":"","mode":"edit"} /-->
<!-- wp:acf/spacer {"id":"block_61c9f74f424f2","name":"acf/spacer","data":{"height":"24","_height":"field_61bb4af54d93e","height_xs":"none","_height_xs":"field_61bb4b124d93f"},"align":"","mode":"edit"} /-->
<!-- wp:acf/siderail-promo-cta {"id":"block_61c9f712424f1","name":"acf/siderail-promo-cta","data":{"title":"BloodHub","_title":"field_61bb2e45b6940","body":"NYBC\'s 24/7 web-based ordering system for our hospital partners.","_body":"field_61bb2ea3ce4db","link":{"title":"Login","url":"#","target":""},"_link":"field_61bb2e45b70fb"},"align":"","mode":"edit"} /-->
<!-- /wp:acf/column-sidebar -->
<!-- wp:acf/column-content {"id":"block_61cc79611b2ae","name":"acf/column-content","align":"","mode":"preview"} -->
<!-- wp:acf/news {"id":"block_61cc79771b2af","name":"acf/news","data":{"post_type":"post","_post_type":"field_61cc5fef28fab"},"align":"","mode":"edit"} /-->
<!-- /wp:acf/column-content -->
<!-- /wp:acf/two-column-block -->',
					)
				);

				/**
				 *  Stories Landing pattern
				 */
				register_block_pattern(
					'nybc/stories-landing',
					array(
						'title'      => esc_html__( 'Stories Landing', 'nybc' ),
						'categories' => array( 'nybc' ),
						'content'    => '
<!-- wp:acf/child-page-hero {"id":"block_61cc78f01b2ab","name":"acf/child-page-hero","data":{"tagline":"Optional Tagline","_tagline":"field_61bc7965d737f","image":88,"_image":"field_61bc7977d7380","color":"#008599","_color":"field_61bc79a8d7381","link":{"title":"Optional Call-to-Action","url":"#","target":""},"_link":"field_61bc79ebd7382"},"align":"","mode":"edit"} /-->
<!-- wp:acf/two-column-block {"id":"block_61cc79611b2ac","name":"acf/two-column-block","data":{"decor":"1","_decor":"field_61cc812303ef5"},"align":"","mode":"preview"} -->
<!-- wp:acf/column-sidebar {"id":"block_61cc79611b2ad","name":"acf/column-sidebar","align":"","mode":"preview"} -->
<!-- wp:acf/zip-code-search {"id":"block_61c9f6d8424f0","name":"acf/zip-code-search","data":{"title":"Search donation centers near you","_title":"field_61b9f5c272f4f","input_label":"Enter your ZIP","_input_label":"field_61b9f5f672f53","link":{"title":"Lookup","url":"#","target":"_blank"},"_link":"field_61b9f5f672f52","description_title":"Did You Know?","_description_title":"field_61b9f5f572f51","description":"One pint of blood can save as many as three lives. There are donor centers in your neighborhood and almost 50 blood drives a day in NY, NJ and beyond.","_description":"field_61b9f5f372f50"},"align":"","mode":"edit"} /-->
<!-- wp:acf/spacer {"id":"block_61c9f74f424f2","name":"acf/spacer","data":{"height":"24","_height":"field_61bb4af54d93e","height_xs":"none","_height_xs":"field_61bb4b124d93f"},"align":"","mode":"edit"} /-->
<!-- wp:acf/siderail-promo-cta {"id":"block_61c9f712424f1","name":"acf/siderail-promo-cta","data":{"title":"BloodHub","_title":"field_61bb2e45b6940","body":"NYBC\'s 24/7 web-based ordering system for our hospital partners.","_body":"field_61bb2ea3ce4db","link":{"title":"Login","url":"#","target":""},"_link":"field_61bb2e45b70fb"},"align":"","mode":"edit"} /-->
<!-- /wp:acf/column-sidebar -->
<!-- wp:acf/column-content {"id":"block_61cc79611b2ae","name":"acf/column-content","align":"","mode":"preview"} -->
<!-- wp:acf/news {"id":"block_61cc79771b2af","name":"acf/news","data":{"field_61cc5fef28fab":"story"},"align":"","mode":"edit"} /-->
<!-- /wp:acf/column-content -->
<!-- /wp:acf/two-column-block -->',
					)
				);
			}
		}
	}

	new NYBC_Block_Patterns();
}
