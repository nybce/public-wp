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

$block_id = 'column-block-' . $block['id'];
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
	'acf/small-card-row',
	'acf/horizontal-cta-card',
	'acf/inline-video',
	'acf/inline-image',
	'acf/accordion',
	'acf/vertical-card-row',
	'acf/vertical-cta-card',
	'acf/spacer',
	'acf/news',
	'acf/article-byline',
	'acf/download-card',
	'acf/download-card-container',
	'acf/graphic-download-card',
	'acf/resource-cards',
	'acf/carousel-video',
);

$class_name .= ' col-lg-8';
?>

<div class="<?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<?php NYBC_Helpers::sidebar_nav( true ); ?>
	<InnerBlocks allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>" templateLock="false"/>
</div>


