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

$block_id = 'graphic-download-card-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$image       = get_field( 'image' );
$downloads   = get_field( 'downloads' );
if ( empty( $downloads ) ) {
	$downloads = array();
}
?>
<div class="graphic-download-card-wrapper mb-16 <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="graphic-download-card">
		<div class="graphic-download-card-img">

			<?php NYBC_Helpers::picture( $image, '160x' ); ?>

		</div>

		<div class="graphic-download-card-info">
			<a href="" class="h5 title fw-800"><?php echo esc_html( $block_title ); ?></a>

			<div class="spacer-8"></div>

			<ul class="download-btn-list">
				<?php foreach ( $downloads as $download ) { ?>
				<li>
					<a class="download-btn" href="<?php echo esc_url( $download['media'] ); ?>" <?php echo esc_html( $download['media'] ? 'download' : '' ); ?>>
						<span><?php echo esc_html( $download['label'] ); ?></span>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd"
								d="M18.4425 9.4425L19.5 10.5L12 18L4.5 10.5L5.5575 9.4425L11.25 15.1275V1.5H12.75V15.1275L18.4425 9.4425ZM19.5 21V18H21V21C21 21.3978 20.842 21.7794 20.5607 22.0607C20.2794 22.342 19.8978 22.5 19.5 22.5H4.5C4.10218 22.5 3.72064 22.342 3.43934 22.0607C3.15804 21.7794 3 21.3978 3 21V18H4.5V21H19.5Z"
								fill="#FC1921" />
						</svg>
					</a>
				</li>
				<?php } ?>
			</ul>

		</div>
	</div>
</div>
