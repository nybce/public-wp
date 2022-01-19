<?php
/**
 * Block Template.
 *
 * @file
 * @package NYBC
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$block_id = 'recent-news-feed-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}
$block_title = get_field( 'title' );
$news        = get_field( 'news' );
$count       = get_field( 'count' );
$lnk         = get_field( 'link' );

if ( ! empty( $news ) ) {
	$count = 0;
}

$news = get_posts(
	array(
		'post_type'   => 'post',
		'numberposts' => ! empty( $count ) ? $count : 3,
		'include'     => ! empty( $news ) ? $news : null,
		'fields'      => 'ids',
	)
);

if ( empty( $news ) ) {
	return;
}

$blog_link = '';
if ( empty( $lnk ) ) {
	$page_for_posts = get_field( 'news_page', 'options' );
	if ( ! empty( $page_for_posts ) ) {
		$blog_link = get_permalink( $page_for_posts );
	}
} else {
	$blog_link = $lnk['url'];
}
?>

<div class="section news <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="container">

		<div class="row">
			<div class="col-12 d-flex justify-content-between">
				<div class="title-wrapper">
					<div class="h4 title fw-800">
						<?php echo esc_html( $block_title ); ?>
					</div>
					<?php if ( $blog_link ) { ?>
						<a href="<?php echo esc_url( $blog_link ); ?>" class="btn-link btn-link-primary right"><?php echo ( ! empty( $lnk ) ? esc_html( $lnk['title'] ) : esc_html__( 'View All', 'nybc' ) ); ?></a>
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
							<?php if ( ! empty( $image ) ) { ?>
							<div class="news-img">
								<?php NYBC_Helpers::picture( $image ); ?>
							</div>
							<?php } ?>
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
