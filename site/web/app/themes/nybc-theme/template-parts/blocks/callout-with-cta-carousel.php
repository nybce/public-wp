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

$block_id = 'callout-with-cta-carousel-' . $block['id'];
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

<div class="section info-block <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="container">
		<div class="swiper-entry info-block-swiper">

			<div class="swiper-button-wrapper">
				<div class="swiper-button-prev"><i></i></div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-next"><i></i></div>
			</div>

			<div class="swiper-container"
				data-options='{"slidesPerView": 1, "spaceBetween": 96, "breakpoints":{"767":{"spaceBetween": 44}}}'>
				<div class="swiper-wrapper">
					<?php
					foreach ( $slides as $slide ) {
						$image = $slide['image'];
						?>
					<div class="swiper-slide">
						<div class="row">
							<?php if ( 'Right' === $slide['image_position'] ) { ?>
							<div class="col-lg-6 align-self-stretch pe-md-0 order-2 order-lg-0">
								<div class="info-block-item">

									<div class="h6 title fw-900 tagline"><?php echo esc_html( $slide['tagline'] ); ?></div>

									<div class="spacer-48 spacer-xs-16"></div>

									<div class="text text-10">
										<?php echo esc_html( $slide['content'] ); ?>
									</div>

									<?php if ( ! empty( $slide['link'] ) ) { ?>
										<div class="spacer-24 spacer-xs-16"></div>
										<a class="btn-link btn-link-primary right" target="<?php echo esc_attr( $slide['link']['target'] ); ?>" href="<?php echo esc_url( $slide['link']['url'] ); ?>"><?php echo esc_html( $slide['link']['title'] ); ?></a>
									<?php } ?>

								</div>
							</div>
							<div class="col-lg-6 ps-md-0">
								<div class="info-block-img">
									<div class="decor-bg rellax" data-rellax-speed="1" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/background-shape.svg');"></div>
									<div class="decor-ring rellax" data-rellax-speed="2" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/ring.svg');"></div>
									<?php NYBC_Helpers::picture( $image, '800x', 'mask' ); ?>
								</div>
							</div>
							<?php } else { ?>
									<div class="col-lg-6 pe-md-0">
										<div class="info-block-img">
											<div class="decor-bg rellax" data-rellax-speed="1"
												style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/background-shape.svg');"></div>
											<div class="decor-ring rellax" data-rellax-speed="2"
												style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/ring.svg');"></div>
											<?php NYBC_Helpers::picture( $image, '800x', 'mask' ); ?>
										</div>
									</div>

									<div class="col-lg-6 align-self-stretch ps-md-0 order-2 order-lg-0">
										<div class="info-block-item type-2">

											<div class="h6 title fw-900 tagline"><?php echo esc_html( $slide['tagline'] ); ?></div>

											<div class="spacer-48 spacer-xs-16"></div>

											<div class="text text-10">
												<?php echo esc_html( $slide['content'] ); ?>
											</div>

											<?php if ( ! empty( $slide['link'] ) ) { ?>
												<div class="spacer-24 spacer-xs-16"></div>
												<a class="btn-link btn-link-primary right" target="<?php echo esc_attr( $slide['link']['target'] ); ?>" href="<?php echo esc_url( $slide['link']['url'] ); ?>"><?php echo esc_html( $slide['link']['title'] ); ?></a>
											<?php } ?>
										</div>
									</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<div class="spacer-64"></div>

</div>
