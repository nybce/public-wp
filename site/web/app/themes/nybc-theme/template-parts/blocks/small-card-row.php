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

$block_id = 'small-card-row-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$cards = get_field( 'cards' );
if ( empty( $cards ) ) {
	return;
}

?>
<div class="swiper-entry card-swiper mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="swiper-button-wrapper">
		<div class="swiper-button-prev"><i></i></div>
		<div class="swiper-pagination"></div>
		<div class="swiper-button-next"><i></i></div>
	</div>

	<div class="swiper-container"
		data-options='{"slidesPerView":3, "spaceBetween": 24, "breakpoints":{"767":{"slidesPerView": 2, "spaceBetween": 16}, "575":{"slidesPerView": "auto", "spaceBetween": 16}}}'>
		<div class="swiper-wrapper">
			<?php foreach ( $cards as $card ) { ?>
			<div class="swiper-slide">
				<a href="<?php echo esc_url( ! empty( $card['link'] ) ? $card['link']['url'] : '#' ); ?>" class="card-item small">
					<?php if ( ! empty( $card['icon'] ) ) { ?>
						<div class="card-img">
							<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/picker-icons/<?php echo esc_html( $card['icon'] ); ?>.svg" alt="icon">
						</div>
					<?php } ?>
					<div class="h6 tagline"><?php echo esc_html( $card['tagline'] ); ?></div>

					<div class="spacer-24"></div>

					<div class="h5 title fw-800"><?php echo esc_html( $card['title'] ); ?></div>

					<div class="spacer-8"></div>

					<div class="text text-20">
						<?php echo esc_html( $card['text'] ); ?>
					</div>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
