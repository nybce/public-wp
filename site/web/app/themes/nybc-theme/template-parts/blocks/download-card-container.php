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

$block_id = 'download-card-container-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}
$block_title = get_field( 'title' );
$text        = get_field( 'text' );
$cards       = get_field( 'cards' );
if ( empty( $cards ) ) {
	return;
}
?>
<div class="download-card-inner <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="spacer-48"></div>

	<div class="text-xl">
		<h3 style="color: #1E2A3A;"><?php echo esc_html( $block_title ); ?></h3>
		<?php if ( ! empty( $text ) ) { ?>
			<div class="spacer-16"></div>
			<p><?php echo esc_html( $text ); ?></p>
		<?php } ?>
	</div>

	<div class="spacer-48"></div>
	<div class="expand-block">
		<div class="download-card-wrapper">
			<?php
			foreach ( $cards as $card ) {
				$card_title = $card['title'];
				$icon       = $card['subtitle'];
				$lnk        = $card['link'];
				$file       = $card['file'];
				if ( empty( $file ) && ! empty( $lnk ) ) {
					$file = $lnk['url'];
				}
				?>
				<a href="<?php echo esc_url( $file ); ?>" class="download-card" <?php echo esc_html( $file ? 'download' : '' ); ?>>
					<div class="download-card-img">
						<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/icons/pdf.svg" alt="">
					</div>
					<div class="download-card-info">
						<div class="h5 title fw-800"><?php echo esc_html( $card_title ); ?></div>

						<div class="spacer-8"></div>

						<div class="text-lg text-20"><?php echo esc_html( $subtitle ); ?></div>
					</div>
				</a>
			<?php } ?>
		</div>

		<div class="expand-more" data-orig-text="<?php esc_html_e( 'Expand', 'nybc' ); ?>"
			data-active-text="<?php esc_html_e( 'Hide', 'nybc' ); ?>"><?php esc_html_e( 'Expand', 'nybc' ); ?></div>
	</div>
</div>

