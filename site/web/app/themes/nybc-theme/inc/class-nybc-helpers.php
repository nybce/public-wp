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
				if ( (int) $nav_item->menu_item_parent === $parent_id ) {
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
		 *  Create img tag
		 *
		 * @param array  $image image data.
		 * @param string $size image size.
		 * @param string $class image class.
		 */
		public static function picture( $image, $size = '', $class = '' ) {
			if ( empty( $image ) ) {
				return;
			}
			$url = ( $size && isset( $image['sizes'][ $size ] ) ) ? $image['sizes'][ $size ] : $image['url'];
			$alt = $image['alt'];

			if ( empty( $url ) ) {
				return;
			}

			if ( ! empty( $class ) ) {
				$class = "class='$class'";
			}
			echo wp_kses(
				"<img {$class} loading=\"lazy\" src=\"{$url}\" alt=\"{$alt}\">",
				array(
					'img' => array(
						'src'     => true,
						'alt'     => true,
						'class'   => true,
						'loading' => true,
					),
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
				<style>
					.no-circle{ 
						background:none!important;
						border:none!important;
						border-radius:0!important;
						width:3em!important;
					}
					.no-circle-dash { 
						background:none!important;
						border:none!important;
						border-radius:0!important;
						width:1em!important;
					}
					li.no-circle:hover a { 
						color: red!important;;
					}

					li.no-circle-dash:hover a{ 
						color: red!important;;
					}
					</style>
				<ul>
						<li class="no-circle"><a href="<?php echo esc_url( get_pagenum_link( 1 ) ); ?>">&laquo; First</a></li>
						<?php if ( $current > 1 ) { ?>
						<li class="no-circle-dash"><a href="<?php echo esc_url( get_pagenum_link( $current - 1 ) ); ?>">&lsaquo;</a></li>
					<?php } ?>

		
					<?php 
					// Calculate pages around the current
					$start = max(1, $current - 1);
					$end = min($max_pages, $current + 4);
		
					// Adjust the range in case we're at the beginning or end of the page range
					if ($current == 1) {
						$end = min($max_pages, 6);
					}
					if ($current == $max_pages) {
						$start = max(1, $max_pages - 5);
					}
		
					for ($i = $start; $i <= $end; $i++) {
						echo '<li' . ($current === $i ? ' class="active"' : '') . '><a href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a></li>';
					}
		
					if ($end < $max_pages) {
						echo '<li class="dots">...</li>';
						echo '<li' . ($current === $max_pages ? ' class="active"' : '') . '><a href="' . esc_url(get_pagenum_link($max_pages)) . '">' . $max_pages . '</a></li>';
					}
		
					if ( $current < $max_pages ) { ?>
						<li class="no-circle-dash"><a href="<?php echo esc_url( get_pagenum_link( $current + 1) ); ?>">&rsaquo;</a></li>
						<li class="no-circle"><a href="<?php echo esc_url( get_pagenum_link( $max_pages ) ); ?>">Last &raquo;</a></li>
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
			$cats = get_tags();
			if ( empty( $cats ) ) {
				return;
			}
			$bydate = '';
			if ( isset( $_GET['bydate'] ) && ! empty( $_GET['bydate'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'filter' ) ) {
				$bydate = sanitize_text_field( wp_unslash( $_GET['bydate'] ) );
			}

			$selected       = array();
			$selected_terms = ( isset( $_GET['terms'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'filter' ) ) ? sanitize_text_field( wp_unslash( $_GET['terms'] ) ) : '';
			if ( $selected_terms ) {
				$selected = explode( ',', $selected_terms );
			}
			?>
			<div class="filter-sidebar <?php echo esc_attr( $mobile ? 'mobile' : '' ); ?>">
				<div class="filter-sidebar-head">
					<div class="h6 title fw-900"><?php esc_html_e( 'Filters', 'nybc' ); ?></div>

					<div class="mobile-button-wrapper">
						<div class="filter-mobile-button"><span></span></div>
					</div>
				</div>

				<div class="filter-sidebar-item">

					<div class="filter-sidebar-title"><?php esc_html_e( 'Filter by Topics', 'nybc' ); ?></div>

					<div class="filter-sidebar-inner">
						<form>
							<?php wp_nonce_field( 'filter', 'nonce' ); ?>
							<input type="hidden" name="terms" value="">
							<input type="hidden" name="bydate" value="<?php echo esc_attr( $bydate ); ?>">
							<ul>
								<li class="tag all" tabindex="0"><?php esc_html_e( 'View All', 'nybc' ); ?><i></i></li>
								<?php foreach ( $cats as $cat ) { ?>
								<li class="tag <?php echo esc_attr( in_array( '' . $cat->term_id, $selected, true ) ? 'active' : '' ); ?>" data-id="<?php echo esc_attr( $cat->term_id ); ?>" tabindex="0">
									<?php echo esc_html( $cat->name ); ?>
									<i></i>
								</li>
								<?php } ?>
							</ul>
						</form>
					</div>
				</div>
			</div>
			<?php if ( ! $mobile ) { ?>
				<script>
					jQuery(function ($) {
						$('.filter-sidebar .tag').on('click', function () {
							let tag = $(this);
							if($(this).hasClass('all')) {
								$('input[name=terms]').val('');
								tag.closest('form').submit();
								return false;
							}
							setTimeout( function() {
								let tags = [];
								tag.closest('.filter-sidebar').find('.tag.active').each( function () {
										let id = $(this).data('id');
										if(tags.indexOf(id) === -1) tags.push(id);
									}
								);
								$('input[name=terms]').val(tags.join(','));
								tag.closest('form').submit();
							}, 200);
						});
					});
				</script>
				<div class="spacer-24"></div>
				<?php
			}
		}

		/**
		 *  Sidebar pages nav
		 *
		 * @param bool $mobile is mobile nav.
		 */
		public static function sidebar_nav( $mobile = false ) {
			global $post;

			$news_page    = get_field( 'news_page', 'options' );
			$stories_page = get_field( 'stories_page', 'options' );
			if ( ( $news_page && is_page( $news_page ) ) || ( $stories_page && is_page( $stories_page ) ) || is_archive() ) {
				self::sidebar_tags( $mobile );
				return;
			}

			if ( empty( $post ) ) {
				return;
			}

			$child_pages = get_pages(
				array(
					'parent'      => $post->ID,
					'sort_column' => 'menu_order',
					'sort_order'  => 'ASC',
				)
			);

			$heading = get_the_title( $post );

			if ( empty( $child_pages ) && $post->post_parent ) {

				$child_pages      = get_pages(
					array(
						'parent'      => $post->post_parent,
						'sort_column' => 'menu_order',
						'sort_order'  => 'ASC',
					)
				);
				$post_parent_link = get_page_link( $post->post_parent );
				$heading          = "<a href='$post_parent_link'>" . get_the_title( $post->post_parent ) . '</a>';
			}
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
		<li><?php echo wp_kses_post( $heading ); ?></li>
			<?php foreach ( $child_pages as $page ) { ?>
			<li class="<?php echo esc_attr( $page->ID === $post->ID ? 'active' : '' ); ?>"><a href="<?php echo esc_url( get_page_link( $page ) ); ?>"><?php echo esc_html( get_the_title( $page ) ); ?></a></li>
		<?php } ?>
	</ul>
</div>
			<?php if ( ! $mobile ) { ?>
<div class="spacer-24"></div>
				<?php
			}
		}

		/**
		 *  Page breadcrumbs
		 */
		public static function breadcrumbs() {
			$middle_title = '';
			$middle_url   = '';
			if ( is_singular( 'post' ) ) {
				$news_page    = get_field( 'news_page', 'options' );
				$middle_title = get_the_title( $news_page );
				$middle_url   = get_the_permalink( $news_page );

			} elseif ( is_singular( 'staff' ) ) {
				$parent_page = get_field( 'parent_page' );
				if ( ! empty( $parent_page ) ) {
					$middle_title = get_the_title( $parent_page );
					$middle_url   = get_the_permalink( $parent_page );
				}
			} elseif ( is_singular( 'story' ) ) {
				$stories_page = get_field( 'stories_page', 'options' );
				if ( ! empty( $stories_page ) ) {
					$middle_title = get_the_title( $stories_page );
					$middle_url   = get_the_permalink( $stories_page );
				}
			}
			?>
			<ul class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<a href="<?php echo esc_url( NYBC_HOME_URI ); ?>" itemprop="item">
						<span itemprop="name"><?php esc_html_e( 'Home', 'nybc' ); ?></span></a>
						<meta itemprop="position" content="1" />
				</li>
				<?php if ( ! empty( $middle_title ) && ! empty( $middle_url ) ) { ?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<a href="<?php echo esc_url( $middle_url ); ?>" itemprop="item">
						<span itemprop="name"><?php echo esc_html( $middle_title ); ?></span>
					</a>
					<meta itemprop="position" content="2" />
				</li>
				<?php } ?>
				<li class="active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<span itemprop="name"><?php the_title(); ?></span>
					<meta itemprop="position" content="3" />
				</li>
			</ul>
			<?php
		}


		/**
		 * Recursive Breadcrumbs
		 * 
		 * @param string $id post id
		 * @param int $pos starting position
		 */
		public static function breadcrumb_nav( $id, $pos ) {

		$has_parent = false;
		$curr_post = get_post($id);
		$parent = get_post_parent($id);
		$crumb_count = $pos;
		if($parent){
			$has_parent = true;
			NYBC_Helpers::breadcrumb_nav($parent->ID,$crumb_count++);
		}
		?>
		<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
			<a href="<?php echo get_permalink($curr_post->ID) ?>" itemprop="item">
				<span itemprop="name"><?php echo $curr_post->post_title ?></span>
			</a>
			<meta itemprop="position" content="<?php echo $crumb_count ?>" />
		</li>
		<?php
		} 

		/**
		 *  Convert phone to tel tag
		 *
		 * @param string $phone phone.
		 */
		public static function tel( $phone ) {
			return str_replace( array( '(', ')', ' ', '-', '.' ), '', $phone );
		}

		/**
		 *  Convert url data array to url
		 *
		 * @param array $url_array url data array.
		 *
		 * @return string
		 */
		public static function build_url( $url_array ) {
			return sprintf(
				'%s://%s%s%s%s',
				$url_array['scheme'],
				$url_array['host'],
				isset( $url_array['path'] ) ? $url_array['path'] : '',
				isset( $url_array['query'] ) ? '?' . $url_array['query'] : '',
				isset( $url_array['fragment'] ) ? '#' . $url_array['fragment'] : ''
			);
		}

		/**
		 *  Get site domain
		 *
		 * @param int $site_id site ID.
		 *
		 * @return string
		 */
		public static function get_site_host( $site_id ) {
			global $wpdb;
			$query = "SELECT domain FROM {$wpdb->blogs} WHERE blog_id = '$site_id'";
			// @codingStandardsIgnoreStart
			$result = $wpdb->get_var( $query );
			// @codingStandardsIgnoreEnd
			return ! empty( $result ) ? $result : null;
		}

		/**
		 *  Get post real link
		 *
		 * @param object $pst post data.
		 * @param string $site_host site domain.
		 *
		 * @return string
		 */
		public static function get_post_real_url( $pst, $site_host ) {
			$lnk        = get_permalink( $pst );
			$lnk_parsed = wp_parse_url( $lnk );

			if ( $lnk_parsed && $site_host && $lnk_parsed['host'] !== $site_host ) {
				$lnk_parsed['host'] = $site_host;
				$lnk                = self::build_url( $lnk_parsed );
			}
			return $lnk;
		}
	}
}
