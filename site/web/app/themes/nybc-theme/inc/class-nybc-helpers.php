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

get_template_part( 'lib/class-aq-resize' );

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
		 * @param string $width image alt.
		 * @param string $height image alt.
		 * @param string $crop image alt.
		 */
		public static function picture( $url, $alt, $width = null, $height = null, $crop = null ) {
			if ( empty( $url ) ) {
				return;
			}
			if ( 'svg' === pathinfo( $url, PATHINFO_EXTENSION ) ) {
				echo wp_kses(
					"<img src=\"{$url}\" alt=\"{$alt}\">",
					array(
						'img' => array(
							'src' => true,
							'alt' => true,
						),
					)
				);
				return;
			}
			if ( ! empty( $width ) ) {
				$url = aq_resize( $url, $width, $height, $crop, true, $crop );
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

		/**
		 *  Pagination
		 *
		 * @param string $max_pages number of pages.
		 */
		public static function pagination( $max_pages = null ) {
			global $paged;
			$current = $paged;
			if ( empty( $current ) ) {
				$current = 1;
			}
			if ( ! $max_pages ) {
				global $wp_query;
				$max_pages = $wp_query->max_num_pages;
				if ( ! $max_pages ) {
					$max_pages = 1;
				}
			}
			$current   = (int) $current;
			$max_pages = (int) $max_pages;

			if ( $max_pages < 2 ) {
				return;
			}
			?>
<div class="pagination">
	<ul>
			<?php if ( $current > 1 ) { ?>
			<li><a class="pagination-arrow left" href="<?php echo esc_url( get_pagenum_link( $current - 1 ) ); ?>"><i></i></a></li>
		<?php } ?>
		<li class="<?php echo esc_attr( 1 === $current ? 'active' : '' ); ?>">
			<a href="<?php echo esc_url( get_pagenum_link( 1 ) ); ?>">1</a>
		</li>
			<?php if ( ( $max_pages - 1 ) > 1 ) { ?>
		<li class="dots">...
			<ul class="dots-select">
				<?php for ( $i = 2; $i <= $max_pages - 1; $i++ ) { ?>
					<li class="dots-select-link <?php echo esc_attr( $i === $current ? 'active' : '' ); ?>"><a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>"><?php echo esc_html( $i ); ?></a></li>
				<?php } ?>
			</ul>
		</li>
		<?php } ?>
		<li class="<?php echo esc_attr( $max_pages === $current ? 'active' : '' ); ?>">
			<a href="<?php echo esc_url( get_pagenum_link( $max_pages ) ); ?>"><?php echo esc_html( $max_pages ); ?></a>
		</li>
			<?php if ( $current < $max_pages ) { ?>
			<li><a class="pagination-arrow right" href="<?php echo esc_url( get_pagenum_link( $current + 1 ) ); ?>"><i></i></a></li>
		<?php } ?>
	</ul>
</div>
			<?php
		}

		/**
		 *  Sidebar tags
		 *
		 * @param bool $mobile is mobile nav.
		 */
		public static function sidebar_tags( $mobile = false ) {
			// TODO: Sidebar tags.
		}

		/**
		 *  Sidebar pages nav
		 *
		 * @param bool $mobile is mobile nav.
		 */
		public static function sidebar_nav( $mobile = false ) {
			global $post;

			if ( empty( $post ) ) {
				return;
			}

			if ( ! is_page() ) {
				self::sidebar_tags( $mobile );
				return;
			}

			$child_pages = get_pages(
				array(
					'parent' => $post->ID,
				)
			);
			if ( empty( $child_pages ) ) {
				return;
			}
			?>
<div class="page-menu-wrapper <?php echo esc_attr( $mobile ? 'mobile' : '' ); ?>">
	<div class="page-menu-head">
		<div class="h6 title fw-900"><?php esc_html_e( 'In this section', 'nybc' ); ?></div>

		<div class="mobile-button-wrapper">
			<div class="page-mobile-button"><span></span></div>
		</div>
	</div>

	<div class="spacer-16"></div>
	<ul class="page-menu">
		<li><?php echo esc_html( get_the_title( $post ) ); ?></li>
			<?php foreach ( $child_pages as $page ) { ?>
			<li><a href="<?php echo esc_url( get_page_link( $page ) ); ?>"><?php echo esc_html( get_the_title( $page ) ); ?></a></li>
		<?php } ?>
	</ul>
</div>
			<?php
		}

	}
}
