<?php
/**
 * NYBC Theme Helper class
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Helpers' ) ) {
	/**
	 * NYBC Theme Helper class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Helpers {

		/**
		 *  Get menu items tree
		 *
		 *  @param array $nav menu items array.
		 *  @param int   $parent_id parent item id.
		 *  @return array
		 */
		public static function menu_tree( array &$nav, $parent_id = 0 ) {
			$branch = array();

			foreach ( $nav as &$nav_item ) {
				if ( $nav_item->menu_item_parent === $parent_id ) {
					$children = self::menu_tree( $nav, $nav_item->ID );
					if ( $children ) {
						$nav_item->children = $children;
					}

					$branch[] = $nav_item;
					unset( $nav_item );
				}
			}

			return $branch;
		}

		/**
		 *  Get all sites logo
		 *
		 * @return array
		 */
		public static function get_sites_logo() {
			$two_line_logo = array();
			if ( is_multisite() ) {
				$sites = get_sites(
					array(
						'fields' => 'ids',
					)
				);
				foreach ( $sites as $site_id ) {
					switch_to_blog( $site_id );
					$two_line_logo[] = get_field( 'two_line_logo', 'options' );
					restore_current_blog();
				}
			} else {
				$two_line_logo[] = get_field( 'two_line_logo', 'options' );
			}
			return $two_line_logo;
		}
	}
}
