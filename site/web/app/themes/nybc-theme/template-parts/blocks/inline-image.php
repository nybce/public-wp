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

$block_id = 'inline-video-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$image   = get_field( 'image' );
$caption = get_field( 'caption' );

if ( empty( $image ) ) {
	return;
}

?>
<div class="text <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<?php NYBC_Helpers::picture( $image, '1062x' ); ?>

	<?php if ( ! empty( $caption ) ) { ?>
		<span><?php echo esc_html( $caption ); ?></span>
	<?php } ?>
</div>
