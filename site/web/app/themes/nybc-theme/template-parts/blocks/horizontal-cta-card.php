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

$block_id = 'horizontal-cta-card-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$image       = get_field( 'image' );
$content     = get_field( 'content' );
$lnk         = get_field( 'link' );
?>
<div class="cta-card-wrapper <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<a target="<?php echo esc_attr( ! empty( $lnk ) ? $lnk['target'] : '' ); ?>" href="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '#' ); ?>" class="cta-card horizontal">
		<div class="cta-card-img">
			<?php NYBC_Helpers::picture( $image, '380x325' ); ?>
		</div>

		<div class="cta-card-info">
			<div class="h4 title fw-800"><?php echo esc_html( $block_title ); ?></div>

			<div class="spacer-16 spacer-xs-8"></div>

			<div class="text-xl text-20">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		</div>

	</a>
	<div class="spacer-0"></div>
</div>
