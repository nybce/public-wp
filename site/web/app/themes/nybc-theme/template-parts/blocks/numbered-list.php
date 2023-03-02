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

$block_id = 'numbered-list-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$numbered_list = get_field( 'numbered_list' );

$class_name = '';
if ( ! empty( $block['className'] ) ) {
    $class_name .= $block['className'];
}

?>
<?php if ( $numbered_list ) { ?>
<ol class="numbered-list <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
    <?php foreach ( $numbered_list as $item) { ?>
        <li class="numbered-list-item">
            <?php if ( $item['item'] ) { ?>
                <?php echo $item['item']; ?>
            <?php } ?>
        </li>
    <?php } ?>
</ol>
<?php } ?>
