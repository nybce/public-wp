<?php
/**
 * Block Template.
 *
 * @file
 * @package NYBC
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$block_id = 'large-card-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$icon        = get_field( 'icon' );
$lnk         = get_field( 'link' );
$text        = get_field( 'text' );
?>
<a href="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '#' ); ?>" class="card-item large <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<?php if ( ! empty( $icon ) ) { ?>
	<div class="card-img">
		<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/picker-icons/<?php echo esc_attr( $icon ); ?>.svg" alt="<?php echo esc_attr( $icon ); ?>">
	</div>
	<?php } ?>
	<div class="spacer-24"></div>

	<div class="h4 title fw-800"><?php echo esc_html( $block_title ); ?></div>

	<div class="spacer-8"></div>

	<div class="text text-20">
		<?php echo wp_kses_post( $text ); ?>
	</div>
</a>

