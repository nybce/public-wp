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

$block_id = 'parent-page-hero-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$tagline     = get_field( 'tagline' );
$image       = get_field( 'image' );
$color       = get_field( 'color' );
$button_link = get_field( 'link' );

$post_title = get_the_title( $post_id );
$image_url  = ! empty( $image ) ? $image['sizes']['1915x'] : get_the_post_thumbnail_url( $post_id, '1915x' );
?>
<div class="section banner <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="banner-inner type-2">
		<div class="banner-wrapper">
			<div class="container container-lg">
				<div class="row justify-content-center">
					<div class="col-lg-10">
						<div class="banner-content">

							<div class="h5 title fw-800 light"><?php echo esc_html( $tagline ); ?></div>

							<div class="spacer-24"></div>

							<h1 class="h1 title fw-800 light"><?php echo esc_html( $post_title ); ?></h1>

							<?php if ( ! empty( $button_link ) ) { ?>
								<div class="spacer-24"></div>
								<a class="btn btn-small btn-primary" target="<?php echo esc_attr( $button_link['target'] ); ?>" href="<?php echo esc_url( $button_link['url'] ); ?>"><?php echo esc_html( $button_link['title'] ); ?></a>
							<?php } ?>

						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if ( ! empty( $image_url ) ) { ?>
			<div class="opacity"></div>
			<div class="bg rellax" style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></div>
		<?php } elseif ( ! empty( $color ) ) { ?>
			<div class="bg" style="background-color: <?php echo esc_attr( $color ); ?>"></div>
		<?php } ?>
	</div>
	<div class="spacer-48 spacer-xs-32"></div>

</div>
