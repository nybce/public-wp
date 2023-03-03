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

$block_id = 'full-width-text-cta-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$title    = get_field( 'title' );
$content  = get_field( 'content' );
$link     = get_field( 'link' );
$url	  = $link['url'];
$text	  = $link['title'];
$target   = $link['target'];

$class_name = '';
if ( ! empty( $block['className'] ) ) {
    $class_name .= $block['className'];
}

?>
<div class="container">
<div class="full-width-text-cta <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
    <?php if ($title) { ?>
        <div class="full-width-text-cta-title h4 title text-40"><?php echo $title ?></div>
    <?php } ?>    
    <div class="full-width-text-cta-content">
        <?php if ($content) { ?>
            <p class="text text-30"><?php echo $content ?></p>
        <?php } ?>
        <?php if ($link) { ?>
            <a class="btn btn-secondary" href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $text ?></a>
        <?php } ?>
    </div>
</div>
</div>