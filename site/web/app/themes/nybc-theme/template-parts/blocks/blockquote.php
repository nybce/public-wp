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

$block_id = 'blockquote-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$text     = get_field( 'text' );
$author   = get_field( 'author' );
$position = get_field( 'position' );
?>
<div class="text mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<blockquote>
		<q><?php echo esc_html( $text ); ?></q>
		<h5><?php echo esc_html( $author ); ?></h5>
		<p><?php echo esc_html( $position ); ?></p>
	</blockquote>
</div>
