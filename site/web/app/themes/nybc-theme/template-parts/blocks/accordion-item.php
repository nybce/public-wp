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

$block_id = 'accordion-item-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title    = get_field( 'title' );
$allowed_blocks = array(
	'core/heading',
	'core/list',
	'core/quote',
	'core/paragraph',
);
?>

<div class="accordion-item <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="accordion-title"><?php echo esc_attr( $block_title ); ?></div>
	<div class="accordion-inner">
		<div class="text-lg">
			<InnerBlocks allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>"/>
		</div>
	</div>
</div>

