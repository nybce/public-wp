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

$block_id = 'employee-spotlight-carousel-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$slides = get_field( 'slides' );
if ( empty( $slides ) ) {
	$slides = array();
}
?>

<div class="section employees <?php echo esc_html( $class_name ); ?>" id="<?php echo esc_html( $block_id ); ?>">

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
					if ( empty( $slide['employee'] ) ) {
						continue;
					}
					$image_url = get_the_post_thumbnail_url( $slide['employee'], '800x' );
					$image     = array(
						'url' => $image_url,
						'alt' => get_the_title( $slide['employee'] ),
					);

					$titles   = get_field( 'titles', $slide['employee'] );
					$position = ! empty( $titles ) ? array_shift( $titles )['title'] : '';

					if ( empty( $position ) ) {
						$position = get_field( 'positions', $slide['employee'] );
						$position = ! empty( $position ) ? array_shift( $position )['position'] : '';
					}

					?>
					<div class="swiper-slide">
					<div class="employees-wrapper">
						<div class="row">
							<div class="col-lg-7 align-self-stretch pe-md-0 order-2 order-lg-0">
								<div class="employees-info">

									<div class="h6 title fw-900 tagline"><?php esc_html_e( 'Employee Spotlight', 'nybc' ); ?></div>

									<div class="spacer-64 spacer-xs-16"></div>

									<div class="text light">
										“<?php echo esc_html( $slide['quote'] ); ?>”
									</div>

									<div class="spacer-24 spacer-xs-16"></div>

									<div class="h5 title fw-800 light name"><?php echo esc_html( get_the_title( $slide['employee'] ) ); ?></div>

									<div class="h6 title fw-500 position"><?php echo esc_html( $position ); ?></div>

									<div class="spacer-48 spacer-xs-16"></div>

									<a href="<?php echo esc_url( get_the_permalink( $slide['employee'] ) ); ?>" class="btn-link btn-link-secondary right"><?php esc_html_e( 'Learn More', 'nybc' ); ?></a>

								</div>
							</div>
							<div class="col-lg-5 ps-lg-0">
								<div class="employees-img">
									<?php NYBC_Helpers::picture( $image ); ?>
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
