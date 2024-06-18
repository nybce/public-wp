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

$button_type    = get_field( 'type' );


$class_name = '';
if ( ! empty( $block['className'] ) ) {
    $class_name .= $block['className'];
}
if( $align == 'Center' ){
    $class_name .= ' text-center';
}else if( $align == 'Right' ){
    $class_name .= ' text-right';
}

$class_name_type = '';
$class_name_font_size ='';
if( $button_type == 'small-white' ){
    $class_name_type .= 'btn-small btn-primary';
}else if( $button_type == 'big-blue'){
	$class_name_type .= 'btn-custom';
    $class_name_font_size .= 'text';
}


?>
<!-- <div class="text mb-24 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<a class="btn btn-custom" href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $title ?></a>
</div> -->

<div class="mb-24 <?php echo esc_attr($class_name_font_size); ?> <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<!-- <a class="btn btn-small btn-primary" href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $title ?></a> -->

	<a class="btn <?php echo esc_attr( $class_name_type ); ?>" href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $title ?></a>
</div>
