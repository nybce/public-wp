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

$block_id = 'download-card-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$subtitle    = get_field( 'subtitle' );
$lnk         = get_field( 'link' );
$file        = get_field( 'file' );
if ( empty( $file ) && ! empty( $lnk ) ) {
	$file = $lnk['url'];
}
?>

<div class="download-card-wrapper mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<a href="<?php echo esc_url( $file ); ?>" class="download-card" <?php echo esc_html( $file ? 'download' : '' ); ?>>
		<div class="download-card-img">
			<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/icons/pdf.svg" alt="">
		</div>
		<div class="download-card-info">
			<div class="h5 title fw-800"><?php echo esc_html( $block_title ); ?></div>

			<div class="spacer-8"></div>

			<div class="text-lg text-20"><?php echo esc_html( $subtitle ); ?></div>
		</div>
	</a>
</div>

