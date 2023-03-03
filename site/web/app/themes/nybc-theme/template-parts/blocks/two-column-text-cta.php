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

$block_id = 'two-column-text-cta-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

$column = get_field( 'column' );

$class_name = '';
if ( ! empty( $block['className'] ) ) {
    $class_name .= $block['className'];
}

?>
<?php if ($column) { ?>
    <div class="container">
    <div class="two-column-text-cta-grid <?php if (count($column) < 2){ echo "full-width"; } ?> <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
        <?php foreach ($column as $item) {
            $title    = $item['title'];
            $content  = $item['content'];
            $link     = $item['link'];
            $url	  = $link['url'];
            $text	  = $link['title'];
            $target   = $link['target'];
        ?>
            <div class="two-column-text-cta">
                <?php if ($title) { ?>
                    <div class="two-column-text-cta-title h4 title text-40"><?php echo $title ?></div>
                <?php } ?>    
                <?php if ($content) { ?>
                    <p class="two-column-text-cta-content text-2 text-30">
                        <?php echo $content ?>
                    </p>
                <?php } ?>
                <?php if ($link) { ?>
                    <a class="btn btn-small btn-secondary" href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $text ?></a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    </div>
<?php } ?>
