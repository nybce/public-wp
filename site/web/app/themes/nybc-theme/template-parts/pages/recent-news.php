<?php
/**
 * Staff Recent News
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$news = get_posts(
	array(
		'post_type'   => 'post',
		'numberposts' => 3,
		'fields'      => 'ids',
	)
);

if ( empty( $news ) ) {
	return;
}
$news_page = get_field( 'news_page', 'options' );
$blog_link = ! empty( $news_page ) ? get_the_permalink( $news_page ) : '';
?>
<div class="spacer-96 spacer-xs-64"></div>
<div class="section news">

	<div class="container">

		<div class="row">
			<div class="col-12 d-flex justify-content-between">
				<div class="title-wrapper">
					<div class="h4 title fw-800">
						<?php esc_html_e( 'Recent News', 'nybc' ); ?>
					</div>
					<?php if ( $blog_link ) { ?>
						<a href="<?php echo esc_url( $blog_link ); ?>" class="btn-link btn-link-primary right"><?php esc_html_e( 'View All', 'nybc' ); ?></a>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="spacer-48 spacer-xs-24"></div>

		<div class="swiper-entry news-swiper">

			<div class="swiper-container"
				data-options='{"slidesPerView":3, "spaceBetween": 24, "breakpoints":{"991":{"slidesPerView": 2}, "767":{"slidesPerView": 1, "spaceBetween": 16}}}'>
				<div class="swiper-wrapper">
					<?php
					foreach ( $news as $news_id ) {
						$image_url   = get_the_post_thumbnail_url( $news_id, '519x292' );
						$block_title = get_the_title( $news_id );
						$date        = get_the_time( 'M j, Y', $news_id );
						$tags        = get_the_tags( $news_id );

						$image = array(
							'url' => $image_url,
							'alt' => $block_title,
						);
						?>
						<div class="swiper-slide">
							<a href="<?php echo esc_url( get_the_permalink( $news_id ) ); ?>" class="news-item">
								<div class="news-img">
									<?php NYBC_Helpers::picture( $image ); ?>
								</div>
								<div class="border-top"></div>
								<div class="news-info">
									<?php
									if ( ! empty( $tags ) ) {
										?>
										<ul class="tags-list">
											<?php foreach ( $tags as $tg ) { ?>
												<li><?php echo esc_html( $tg->name ); ?></li>
											<?php } ?>
										</ul>
									<?php } ?>
									<div class="spacer-16"></div>

									<div class="h5 title fw-800"><?php echo esc_html( $block_title ); ?></div>

									<div class="spacer-16"></div>

									<div class="date"><?php echo esc_html( $date ); ?></div>
								</div>
							</a>
						</div>
					<?php } ?>

				</div>
			</div>
			<div class="spacer-xs-16"></div>
			<div class="swiper-button-wrapper">
				<div class="swiper-button-prev"><i></i></div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-next"><i></i></div>
			</div>

		</div>
	</div>
	<div class="spacer-120 spacer-xs-64"></div>
</div>
