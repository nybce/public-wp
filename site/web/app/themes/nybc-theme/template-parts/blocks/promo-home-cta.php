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

$block_id = 'promo-home-cta-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$body        = get_field( 'body' );
$button      = get_field( 'button' );
?>

<div class="section contact-us mt-150 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="contact-block">
					<div class="h4 title fw-800 light"><?php echo esc_html( $block_title ); ?></div>
					<div class="text-xl text-30">
						<?php echo esc_html( $text ); ?>
					</div>
					<a class="btn btn-secondary" target="<?php echo esc_attr( $button['target'] ); ?>" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['title'] ); ?></a>
				</div>
			</div>
		</div>
	</div>

	<div class="spacer-120 spacer-xs-72"></div>
</div>
