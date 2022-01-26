<?php
/**
 * Block Template.
 *
 * @file
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$block_id = 'carousel-video-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$videos = get_field( 'slides' );

if ( empty( $videos ) ) {
	return;
}
?>
<div class="section video <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="container container-lg">
		<div class="swiper-entry video-swiper">

			<div class="swiper-button-wrapper">
				<div class="swiper-button-prev"><i></i></div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-next"><i></i></div>
			</div>

			<div class="swiper-container"
				data-options='{"slidesPerView": 2, "spaceBetween":24, "breakpoints":{"1600":{"slidesPerView": 1}}}'>
				<div class="swiper-wrapper">
					<?php
					foreach ( $videos as $video ) {
						$url  = $video['url'];
						$file = $video['file'];
						if ( empty( $url ) && empty( $file ) ) {
							continue;
						}

						$file_image = $video['file_image'];
						$file_image = ! empty( $file_image ) ? $file_image['sizes']['800x'] : '';

						$caption = $video['caption'];
						?>
					<div class="swiper-slide">
						<div class="text">
							<?php if ( ! empty( $url ) ) { ?>
							<iframe loading="lazy" title=""
									width="500" height="281" src="<?php echo esc_url( $url ); ?>"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
									allowfullscreen="">
							</iframe>
							<?php } else { ?>
								<div class="video-container">
									<div class="video-cover" style="background-image: url(<?php echo esc_url( $file_image ); ?>);">
									</div>
									<div class="video-item html5video">
										<video>
											<source src="<?php echo esc_url( $file ); ?>" type="video/mp4">
										</video>
									</div>
									<div class="playpausebtns">
										<a href="javascript:void();" class="play-video"></a>
										<a href="javascript:void();" class="pause-video"></a>
									</div>
								</div>
							<?php } ?>
							<?php if ( ! empty( $caption ) ) { ?>
								<span><?php echo esc_html( $caption ); ?></span>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>

		</div>
	</div>
    <div class="spacer-64 spacer-xs-48"></div>
</div>
