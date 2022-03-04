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

$block_id = 'full-width-feature-cta-carousel' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}
$slides      = get_field( 'slides' );
$image       = get_field( 'image' );
$block_title = get_field( 'title' );
$body        = get_field( 'body' );
$button      = get_field( 'button' );

if ( empty( $slides ) ) {
	return;
}
?>

<div class="section about-us <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="container container-lg">
		<div class="swiper-entry about-swiper">

			<div class="swiper-button-inner">
				<div class="swiper-button-wrapper">
					<div class="swiper-button-prev"><i></i></div>
					<div class="swiper-pagination"></div>
					<div class="swiper-button-next"><i></i></div>
				</div>
			</div>

			<div class="swiper-container"
				data-options='{"autoplay": {"delay": 5000}, "loop": true, "slidesPerView": 1, "breakpoints":{"767":{"slidesPerView": 1, "spaceBetween": 45}}}'>
				<div class="swiper-wrapper">

					<?php
					foreach ( $slides as $slide ) {
						$image       = $slide['image'];
						$block_title = $slide['title'];
						$body        = $slide['body'];
						$button      = $slide['button'];
						?>
					<div class="swiper-slide">
						<div class="about-item">
							<div class="about-img">
								<?php NYBC_Helpers::picture( $image, '800x' ); ?>
							</div>
							<div class="about-info">
								<div class="spacer-48"></div>
								<div class="h2 title fw-800 light"><?php echo esc_html( $block_title ); ?></div>
								<div class="spacer-16"></div>
								<div class="text-xl text-30"><?php echo esc_html( $body ); ?></div>
								<div class="spacer-24"></div>
								<?php if ( ! empty( $button ) ) { ?>
									<a class="btn btn-primary" target="<?php echo esc_attr( $button['target'] ); ?>" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['title'] ); ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>
	<div class="spacer-120"></div>
</div>
