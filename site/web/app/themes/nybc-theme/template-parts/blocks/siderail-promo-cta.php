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

$block_id = 'siderail-promo-cta-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$body        = get_field( 'body' );
$lnk         = get_field( 'link' );
?>

<div class="promo-cta mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="h4 title fw-800 light"><?php echo esc_html( $block_title ); ?></div>
	<div class="spacer-8"></div>
	<div class="text-lg text-30"><?php echo esc_html( $body ); ?></div>
	<?php if ( ! empty( $lnk ) ) { ?>
		<div class="spacer-24"></div>
		<a class="btn btn-small btn-secondary" target="<?php echo esc_attr( $lnk['target'] ); ?>" href="<?php echo esc_url( $lnk['url'] ); ?>"><?php echo esc_html( $lnk['title'] ); ?></a>
	<?php } ?>
</div>
