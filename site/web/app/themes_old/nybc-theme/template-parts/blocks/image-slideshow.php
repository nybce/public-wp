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

$block_id = 'image-slideshow-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$images = get_field( 'slides' );

if ( empty( $images ) ) {
	return;
}
?>
<div class="image-slideshow-container container">
	<div class="image-slideshow <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
		<div class="swiper-entry">

			<div class="swiper-container swiper-main" data-options='{"slidesPerView": 1, "spaceBetween": 24, 
				"thumbs": {"swiper":{
					"el": "#<?php echo esc_attr( $block_id ); ?> .swiper-thumbs", 
					"slidesPerView": "auto", 
					"spaceBetween": 16,
					"breakpoints":{"678":{
						"spaceBetween": 8,
						"navigation": {
							"prevEl": ".swiper-thumbs .swiper-button-prev",
							"nextEl": ".swiper-thumbs .swiper-button-next"
						}
					}}
					
				}}}'>
				<div class="swiper-wrapper">
					<?php
					foreach ( $images as $image ) {
						$url  = $image['image'];
						$caption = $image['caption'];
						if ( empty( $url ) ) {
							continue;
						}
						?>
					<div class="swiper-slide">
						<div class="swiper-slide-image" style="background-image: url(<?php echo $url; ?>)">
							<img src="<?php echo $url; ?>" alt="">
						</div>
						<?php if ( ! empty( $caption ) ) { ?>
							<div class="caption"><?php echo esc_html( $caption ); ?></div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>

				<div class="swiper-button-wrapper">
					<div class="swiper-button-prev"><i></i></div>
					<div class="swiper-button-next"><i></i></div>
				</div>
			</div>

		</div>
		<div class="swiper-entry">

			<div class="swiper-container swiper-thumbs" data-options='{"slidesPerView": "auto", "spaceBetween": 16, "breakpoints":{"678":{
						"spaceBetween": 8,
						"navigation": {
							"prevEl": ".swiper-thumbs .swiper-button-prev",
							"nextEl": ".swiper-thumbs .swiper-button-next"
						}
					}}}'>
				<div class="swiper-wrapper">
					<?php
					foreach ( $images as $image ) {
						$url  = $image['image'];
						if ( empty( $url ) ) {
							continue;
						}
						?>
					<div class="swiper-slide">
						<div class="swiper-slide-thumb" style="background-image: url(<?php echo $url; ?>)"></div>
					</div>
					<?php } ?>
				</div>

				<div class="swiper-button-wrapper">
					<div class="swiper-button-prev"><i></i></div>
					<div class="swiper-button-next"><i></i></div>
				</div>
			</div>

		</div>
	</div>
</div>
