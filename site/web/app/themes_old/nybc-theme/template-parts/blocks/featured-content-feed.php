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

$block_id = 'featured-content-feed-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title    = get_field( 'title' );
$featured_cards = get_field( 'featured_cards' );
if ( empty( $featured_cards ) ) {
	$featured_cards = array();
}

?>

<div class="section featured <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="container">

		<div class="row">
			<div class="col-12 d-flex justify-content-between">
				<div class="title-wrapper">
					<div class="h4 title fw-800">
						<?php echo esc_html( $block_title ); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="spacer-48 spacer-xs-24"></div>

		<div class="swiper-entry news-swiper">

			<div class="swiper-container"
				data-options='{"slidesPerView":3, "spaceBetween": 24, "breakpoints":{"991":{"slidesPerView": 2}, "767":{"slidesPerView": 1, "spaceBetween": 16}}}'>
				<div class="swiper-wrapper">
					<?php foreach ( $featured_cards as $card ) { ?>
					<div class="swiper-slide">
						<a class="news-item type-2" target="<?php echo esc_attr( $card['link'] ? $card['link']['target'] : '' ); ?>" href="<?php echo esc_url( $card['link'] ? $card['link']['url'] : '' ); ?>">
						<?php
						if ( ! empty( $card['image'] ) ) {
							?>
							<div class="news-img">
								<?php NYBC_Helpers::picture( $card['image'], '519x292' ); ?>
							</div>
							<?php } ?>
							<div class="border-top"></div>

							<div class="news-info">
								<ul class="tags-list">
									<li class="tag-main"><?php echo esc_html( $card['tagline'] ); ?></li>
								</ul>

								<div class="spacer-24"></div>

								<div class="h5 title fw-800"><?php echo esc_html( $card['title'] ); ?></div>

								<?php if ( ! empty( $card['text'] ) ) { ?>
								<div class="spacer-16"></div>

								<div class="text text-20"><?php echo esc_html( $card['text'] ); ?></div>
								<?php } ?>
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
