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

$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title    = get_field( 'title' );
$id_title		= strtolower($block_title);
$id_title		= preg_replace('/[^A-Za-z0-9]/', '', $id_title);
$id_title		= str_replace("-", " ", $id_title);

$block_id 		= 'accordion-item-' . $id_title;

$allowed_blocks = array(
	'core/heading',
	'core/list',
	'acf/blockquote',
	'acf/custom-button',
	'core/paragraph',
	'core/image',
	'gravityforms/form',
	'core/html',
	'core/shortcode',
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

