<?php
/**
 * Block Template.
 *
 * @file
 * @package NYBC
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$block_id = 'two-column-block-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$decor = get_field( 'decor' );

$allowed_blocks = array(
	'acf/column-sidebar',
	'acf/column-content',
);

$template = array(
	array( 'acf/column-sidebar' ),
	array( 'acf/column-content' ),
);

?>

<div class="section <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<?php if ( $decor ) { ?>
		<div class="decor-news" data-rellax-speed="-1" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/wave.svg');"></div>
	<?php } ?>
	<div class="container container-lg">
		<div class="row">
			<InnerBlocks allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>" template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>" templateLock="all"/>
		</div>
	</div>
	<div class="spacer-120"></div>
</div>

