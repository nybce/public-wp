<?php
/**
 * Block Template.
 *
 * @file
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$block_id = 'spacer-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$height    = get_field( 'height' );
$height_xs = get_field( 'height_xs' );
$height_xs = 'none' !== $height_xs ? "spacer-xs-$height_xs" : '';
?>
<div class="spacer-<?php echo esc_attr( $height ); ?> <?php echo esc_attr( $height_xs ); ?> <?php echo esc_attr( $class_name ); ?>"
	id="<?php echo esc_attr( $block_id ); ?>"></div>
