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

$block_id = 'tabbed-card-carousel-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$content     = get_field( 'content' );
$lnk         = get_field( 'link' );
$block_tabs  = get_field( 'tab' );
if ( empty( $block_tabs ) ) {
	$block_tabs = array();
}
?>

<div class="section products-services <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="decor-left rellax" data-rellax-speed="-2" style="background-image: url('<?php echo esc_html( NYBC_IMG_URI ); ?>/decor-left.svg');"></div>

	<div class="container container-lg">

		<div class="row">
			<div class="col-12 d-flex justify-content-between">
				<div class="title-wrapper type-3">
					<div class="h2 title fw-800">
						<?php echo esc_html( $block_title ); ?>
					</div>

					<div class="info">
						<div class="text-xl text-20">
							<?php echo esc_html( $content ); ?>
						</div>
						<div class="spacer-24"></div>
						<?php if ( ! empty( $lnk ) ) { ?>
							<a class="btn-link btn-link-primary right" target="<?php echo esc_attr( $lnk['target'] ); ?>" href="<?php echo esc_url( $lnk['url'] ); ?>"><?php echo esc_html( $lnk['title'] ); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="spacer-24 spacer-xs-48"></div>

		<div class="tabs">

			<div class="tab-nav">
				<div class="tab-title">
					<?php echo esc_html( isset( $block_tabs[0] ) ? $block_tabs[0]['title'] : '' ); ?>
				</div>
				<div class="tab-toggle">
					<?php foreach ( $block_tabs as $i => $block_tab ) { ?>
						<div class="<?php echo esc_attr( 0 === $i ? 'active' : '' ); ?>"><?php echo esc_html( $block_tab['title'] ); ?></div>
					<?php } ?>
				</div>
			</div>
			<?php
			foreach ( $block_tabs as $i => $block_tab ) {
				$cards = ! empty( $block_tab['cards'] ) ? $block_tab['cards'] : array();
				?>
			<div class="tab">

				<div class="swiper-entry card-swiper">
					<div class="swiper-button-wrapper">
						<div class="swiper-button-prev"><i></i></div>
						<div class="swiper-pagination"></div>
						<div class="swiper-button-next"><i></i></div>
					</div>

					<div class="swiper-container"
						data-options='{"slidesPerView":4, "autoHeight": true, "spaceBetween": 24, "breakpoints":{"1199":{"slidesPerView": 3}, "767":{"slidesPerView": 2, "spaceBetween": 16}, "575":{"slidesPerView": "auto", "spaceBetween": 16}}}'>
						<div class="swiper-wrapper">
							<?php foreach ( $cards as $card ) { ?>
							<div class="swiper-slide">

							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="spacer-120 spacer-xs-48"></div>
</div>
