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

$block_id = 'parent-home-hero-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$text        = get_field( 'text' );
$image       = get_field( 'image' );
$button_link = get_field( 'button_link' );

$post_title = get_the_title( $post_id );
$image_url  = ! empty( $image ) ? $image['sizes']['1915x'] : get_the_post_thumbnail_url( $post_id, '1915x' );
?>
<div class="section banner <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="banner-inner type-3">

		<div class="opacity"></div>

		<div class="banner-wrapper">

			<div class="container container-lg">
				<div class="row justify-content-center">
					<div class="col-lg-10">
						<div class="banner-content">

							<div class="h5 title fw-800 light"><?php echo esc_html( $post_title ); ?></div>

							<div class="spacer-48 spacer-xs-24"></div>

							<h1 class="h2 title fw-800 light"><?php echo wp_kses_post( $text ); ?></h1>

							<div class="spacer-48 spacer-xs-24"></div>

							<?php if ( ! empty( $button_link ) ) { ?>
								<a class="btn btn-primary" target="<?php echo esc_attr( $button_link['target'] ); ?>" href="<?php echo esc_url( $button_link['url'] ); ?>"><?php echo esc_html( $button_link['title'] ); ?></a>
							<?php } ?>

						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="bg rellax" data-rellax-speed="-1" style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></div>

	</div>

	<div class="spacer-64 spacer-xs-48"></div>

</div>


