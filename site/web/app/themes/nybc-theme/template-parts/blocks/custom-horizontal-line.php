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

$block_id = 'custom-horizontal-line-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$thickness          = get_field( 'thickness' );
$color              = get_field( 'color' );
$top_spacing        = get_field( 'top_spacing' );
$bottom_spacing     = get_field( 'bottom_spacing' );

$class_name = '';
if ( ! empty( $block['className'] ) ) {
    $class_name .= $block['className'];
}

?>
<div class="custom-horizontal-line <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>" style="
    <?php if ($thickness) { ?>--hr-thickness:<?php echo $thickness; ?>px;<?php } ?>
    <?php if ($color) { ?>--hr-color:<?php echo $color; ?>;<?php } ?>
    <?php if ($top_spacing) { ?>--hr-top-spacing:<?php echo $top_spacing; ?>px;<?php } ?>
    <?php if ($bottom_spacing) { ?>--hr-bottom-spacing:<?php echo $bottom_spacing; ?>px;<?php } ?>
">
</div>
