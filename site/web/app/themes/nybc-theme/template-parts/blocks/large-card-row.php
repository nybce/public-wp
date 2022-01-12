<?php
/**
 * Block Template.
 *
 * @file
 * @package NYBC
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$block_id = 'large-card-row-' . $block['id'];
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

<div class="section products-services mt-70 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="decor-center rellax" data-rellax-speed="-2"></div>

	<div class="container container-lg">

		<div class="swiper-entry card-swiper type-2">
			<div class="swiper-button-wrapper">
				<div class="swiper-button-prev"><i></i></div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-next"><i></i></div>
			</div>

			<div class="swiper-container"
				data-options='{"slidesPerView":3, "spaceBetween": 24, "breakpoints":{"1199":{"slidesPerView": 3},"991":{"slidesPerView": 2}, "767":{"slidesPerView": 1, "spaceBetween": 16}, "575":{"slidesPerView": "auto", "spaceBetween": 16}}}'>
				<div class="swiper-wrapper">
					<?php
					foreach ( $cards as  $card ) {
						$block_title = $card['title'];
						$icon        = $card['icon'];
						$lnk         = $card['link'];
						$text        = $card['text'];
						?>
					<div class="swiper-slide">

						<a href="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '#' ); ?>" class="card-item large">
							<?php if ( ! empty( $icon ) ) { ?>
								<div class="card-img">
									<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/picker-icons/<?php echo esc_attr( $icon ); ?>.svg" alt="<?php echo esc_attr( $icon ); ?>">
								</div>
							<?php } ?>
							<div class="spacer-24"></div>

							<div class="h4 title fw-800"><?php echo esc_html( $block_title ); ?></div>

							<div class="spacer-8"></div>

							<div class="text text-20">
								<?php echo wp_kses_post( $text ); ?>
							</div>
						</a>

					</div>
					<?php } ?>
				</div>
			</div>
		</div>

	</div>

	<div class="spacer-24 spacer-xs-48"></div>

</div>

