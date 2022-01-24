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

$block_id = 'text-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}
$allowed_blocks = array(
	'core/heading',
	'core/list',
	'acf/blockquote',
	'core/paragraph',
	'acf/accordion',
);

$class_name .= ' mb-48';
?>

<div class="<?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<InnerBlocks allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>" templateLock="false"/>
</div>


