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

$block_id = 'home-hero-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$image     = get_field( 'media' );
$video     = get_field( 'video' );
$image_url = ! empty( $image ) ? $image['sizes']['1915x'] : '';

if ( empty( $image ) && empty( $video ) ) {
	$image_url = get_the_post_thumbnail_url( $post_id, '1915x' );
}
$title_text = get_field( 'title' );
$button     = get_field( 'button' );
?>
<div class="section banner <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="banner-inner">
		<div class="banner-decor rellax">
			<svg viewBox="0 0 869 810" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path opacity="0.8" d="M869 409.81C869 539.552 817.375 663.979 725.482 755.72C633.59 847.46 508.956 899 379 899C249.044 899 124.411 847.46 32.5177 755.72C-59.3751 663.979 -111 539.552 -111 409.81C-111 -50.7791 334.532 -410.201 350.495 -425.284C358.415 -431.575 368.238 -435 378.359 -435C388.48 -435 398.302 -431.575 406.222 -425.284C423.468 -410.201 869 -50.7791 869 409.81Z" fill="#E30513" />
			</svg>
		</div>

		<div class="opacity"></div>

		<div class="banner-wrapper">

			<div class="container">
				<div class="row">
					<div class="col-lg-5">
						<div class="banner-content">
							<h1 class="h1 title fw-800 light">
								<?php echo esc_html( $title_text ); ?>
							</h1>
							<div class="spacer-48 spacer-xs-24"></div>
							<?php if ( ! empty( $button ) ) { ?>
								<a class="btn btn-primary" target="<?php echo esc_attr( $button['target'] ); ?>" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['title'] ); ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		if ( ! empty( $image_url ) ) {
			?>

			<div class="bg rellax" style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></div>

		<?php } elseif ( ! empty( $video ) ) { ?>

			<div class="bg video rellax">
				<video src="<?php echo esc_url( $video ); ?>" class="video" autoplay="" loop="" preload="" muted="" playsinline=""></video>
			</div>

		<?php } ?>
	</div>

</div>
