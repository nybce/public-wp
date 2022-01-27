<?php
/**
 * Search Result
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;
$sort = ( isset( $_GET['bydate'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'search' ) ) ? sanitize_text_field( wp_unslash( $_GET['bydate'] ) ) : '';

$posts_query = $wp_query->posts;
?>
<div class="section search-results">

	<div class="decor-search" data-rellax-speed="-1" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/wave-1.svg');"></div>

	<div class="container container-lg">
		<div class="row justify-content-center">
			<div class="col-lg-10">
				<div class="filters-wrapper">
					<div class="select-item-total"><?php esc_html_e( 'Showing', 'nybc' ); ?> <span><?php echo esc_html( $wp_query->found_posts ); ?></span> <?php esc_html_e( 'Results', 'nybc' ); ?></div>

					<div class="select-item ml-auto">
						<form action="<?php echo esc_url( NYBC_HOME_URI ); ?>">
							<?php wp_nonce_field( 'search', 'nonce' ); ?>
							<input type="hidden" name="s" value="<?php echo esc_attr( get_search_query() ); ?>">
							<select class="SelectBox" name="bydate" tabindex="-1" onchange="this.form.submit()">
								<option <?php echo esc_html( ! $sort ? 'selected' : '' ); ?> disabled><?php esc_html_e( 'Sort', 'nybc' ); ?></option>
								<option <?php echo esc_html( 'ASC' === $sort ? 'selected' : '' ); ?> value="ASC"><?php esc_html_e( 'By Date Ascending', 'nybc' ); ?></option>
								<option <?php echo esc_html( 'DESC' === $sort ? 'selected' : '' ); ?> value="DESC"><?php esc_html_e( 'By Date Descending', 'nybc' ); ?></option>
							</select>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="spacer-48 spacer-xs-24"></div>

		<div class="row justify-content-center">
			<div class="col-lg-10">
				<?php if ( ! empty( $posts_query ) ) { ?>
				<div class="search-cards-wrapper">
					<?php
					foreach ( $posts_query as $pst ) {
						$site_host = null;
						if ( isset( $pst->site_id ) ) {
							switch_to_blog( $pst->site_id );
							$site_host = NYBC_Helpers::get_site_host( $pst->site_id );
						}
						$lnk = NYBC_Helpers::get_post_real_url( $pst, $site_host );

						$image_url   = get_the_post_thumbnail_url( $pst, '380x369' );
						$block_title = get_the_title( $pst );
						$image       = array(
							'url' => $image_url,
							'alt' => $block_title,
						);
						?>

					<a href="<?php echo esc_url( $lnk ); ?>" class="search-card">
						<?php if ( ! empty( $image_url ) ) { ?>
							<div class="search-card-img">
								<?php NYBC_Helpers::picture( $image ); ?>
							</div>
						<?php } ?>
						<div class="search-card-info">

							<div class="h4 title fw-800"><?php echo esc_html( $block_title ); ?></div>

							<div class="spacer-16"></div>

							<div class="text-xl text-20"><?php echo esc_html( get_the_excerpt( $pst ) ); ?></div>
						</div>
					</a>
						<?php
						if ( isset( $pst->site_id ) ) {
							restore_current_blog();
						}
					}
					?>

				</div>
				<?php } ?>
			</div>
		</div>

		<div class="spacer-32"></div>

		<div class="row justify-content-center">
			<div class="col-lg-8">
				<?php NYBC_Helpers::pagination(); ?>
			</div>
		</div>
		<div class="spacer-96"></div>
	</div>
</div>
