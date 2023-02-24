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

$block_id = 'donor-stories-carousel-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$slides = get_field( 'slides' );
if ( empty( $slides ) ) {
	return;
}
?>

<div class="section <?php echo esc_html( $class_name ); ?>" id="<?php echo esc_html( $block_id ); ?>">

	<div class="swiper-entry employees-swiper">

		<div class="swiper-button-wrapper">
			<div class="swiper-button-prev"><i></i></div>
			<div class="swiper-pagination"></div>
			<div class="swiper-button-next"><i></i></div>
		</div>

		<div class="swiper-container" data-options='{"slidesPerView": 1}'>
			<div class="swiper-wrapper">
				<?php
				foreach ( $slides as $slide ) {
					$image = $slide['image'];
					?>
					<div class="swiper-slide">
					<div class="container">
						<div class="row">
							<div class="col-lg-7 align-self-stretch pe-md-0 order-2 order-lg-0">
								<div class="employees-info">

									<div class="h6 title fw-900 tagline"><?php !empty($slide['label']) ? 
									esc_html_e( $slide['label'] ) : echo '';
								?></div>

									<div class="spacer-48 spacer-xs-16"></div>

									<div class="text light">
										<?php echo esc_html( $slide['quote'] ); ?>
									</div>

									<div class="spacer-24 spacer-xs-16"></div>

									<div class="text-xl text-30"><?php echo esc_html( $slide['text'] ); ?></div>

									<?php if ( ! empty( $slide['link'] ) ) { ?>
										<div class="spacer-48 spacer-xs-16"></div>
										<a class="btn-link btn-link-secondary right" target="<?php echo esc_attr( $slide['link']['target'] ); ?>" href="<?php echo esc_url( $slide['link']['url'] ); ?>"><?php echo esc_html( $slide['link']['title'] ); ?></a>
									<?php } ?>

								</div>
							</div>
							<div class="col-lg-5 ps-lg-0">
								<div class="employees-img">
									<?php NYBC_Helpers::picture( $image, '800x' ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

	</div>
	<div class="spacer-96 spacer-xs-64"></div>
</div>
