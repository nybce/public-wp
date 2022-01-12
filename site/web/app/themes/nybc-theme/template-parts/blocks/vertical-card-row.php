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

$block_id = 'vertical-cta-card-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$cards = get_field( 'cards' );
if ( empty( $cards ) ) {
	return;
}
?>
<div class="cta-card-wrapper vertical <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<?php
	foreach ( $cards as $card ) {
		$block_title = $card['title'];
		$image       = $card['image'];
		$content     = $card['content'];
		$lnk         = $card['link'];

		if ( empty( $image ) ) {
			$image = get_field( 'two_line_logo', 'options' );
		}
		?>
		<a target="<?php echo esc_attr( ! empty( $lnk ) ? $lnk['target'] : '' ); ?>"
		href="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '#' ); ?>" class="cta-card vertical">

			<div class="cta-card-img <?php echo esc_attr( ( $image && 'svg' === pathinfo( $image['url'], PATHINFO_EXTENSION ) ) ? 'logo' : '' ); ?>">
				<?php NYBC_Helpers::picture( $image, '519x283' ); ?>
			</div>

			<div class="cta-card-info">
				<div class="h4 title fw-800"><?php echo esc_html( $block_title ); ?></div>

				<div class="spacer-16 spacer-xs-8"></div>

				<div class="text-xl text-20">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			</div>

		</a>
	<?php } ?>
</div>
