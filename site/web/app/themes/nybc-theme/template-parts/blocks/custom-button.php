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

$block_id = 'custombutton-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$link     = get_field( 'link' );
$url	  = $link['url'];
$title	  = $link['title'];
$target   = $link['target'];

$align    = get_field( 'alignment' );


$class_name = '';
if ( ! empty( $block['className'] ) ) {
    $class_name .= $block['className'];
}
if( $align == 'Center' ){
    $class_name .= ' text-center';
}else if( $align == 'Right' ){
    $class_name .= ' text-right';
}

?>
<!-- <div class="text mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<a class="btn btn-custom" href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $title ?></a>
</div> -->

<div class="mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<a class="btn btn-small btn-primary" href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $title ?></a>
</div>
