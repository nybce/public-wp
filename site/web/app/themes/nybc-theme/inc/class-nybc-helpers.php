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
		 * @param array $nav menu items array.
		 * @param int   $parent_id parent item id.
		 * @return array
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

		/**
		 *  Convert image to WEBP format
		 *
		 * @param string $url image url.
		 * @return string
		 */
		public static function image_to_webp( $url ) {
			if ( empty( $url ) ) {
				return $url;
			}

			$upload_info = wp_upload_dir();
			$upload_dir  = $upload_info['basedir'];
			$upload_url  = $upload_info['baseurl'];
			$rel_path    = str_replace( $upload_url, '', $url );
			$img_path    = $upload_dir . $rel_path;
			$path_result = preg_replace( '/\.[^.]+$/', '.', $img_path ) . 'webp';

			if ( file_exists( $path_result ) ) {
				return preg_replace( '/\.[^.]+$/', '.', $url ) . 'webp';
			}

			$editor       = wp_get_image_editor( $img_path );
			$resized_file = $editor->save( $path_result, 'image/webp' );

			if ( ! is_wp_error( $resized_file ) ) {
				$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path'] );
				return $upload_url . $resized_rel_path;
			} else {
				return false;
			}

		}

		/**
		 *  Create WEBP picture tag
		 *
		 * @param string $url image url.
		 * @param string $alt image alt.
		 */
		public static function picture( $url, $alt ) {
			if ( empty( $url ) ) {
				return;
			}

			$type = explode( '.', $url );
			$type = ! empty( $type ) ? array_pop( $type ) : '';
			if ( 'jpeg' === $type || 'jpg' === $type ) {
				$type = 'jpeg';
			} elseif ( 'png' === $type ) {
				$type = 'png';
			}

			$webp_url = self::image_to_webp( $url );

			echo wp_kses(
				"
<picture>
<source srcset=\"{$webp_url}\" type=\"image/webp\">
<source srcset=\"{$url}\" type=\"image/{$type}\">
<img src=\"{$url}\" alt=\"{$alt}\" loading='lazy'>
</picture>",
				array(
					'source'  => array(
						'srcset' => true,
						'type'   => true,
					),
					'img'     => array(
						'src'     => true,
						'alt'     => true,
						'loading' => true,
					),
					'picture' => array(),
				)
			);
		}

	}
}
