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

$url     = get_field( 'url' );
$caption = get_field( 'caption' );

if ( empty( $url ) ) {
	return;
}
?>
<div class="text <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<iframe loading="lazy"  width="500"
			height="281" src="<?php echo esc_url( $url ); ?>"
			allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
			allowfullscreen="" style="margin-bottom: 0">
	</iframe>
	<?php if ( ! empty( $caption ) ) { ?>
		<span><?php echo esc_html( $caption ); ?></span>
	<?php } ?>
</div>
