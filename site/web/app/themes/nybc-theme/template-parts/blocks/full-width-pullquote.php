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

$block_id = 'full-width-pullquote-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$image        = get_field( 'image' );
$quote        = get_field( 'quote' );
$source       = get_field( 'source' );
$source_title = get_field( 'source_title' );
?>
<div class="section quotes <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">

	<div class="decor-right rellax" data-rellax-speed="-2" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/decor-right.svg');"></div>

	<div class="container">

		<div class="row">
			<div class="col-lg-5 pe-lg-0">
				<div class="quotes-img">
					<?php NYBC_Helpers::picture( $image, '800x' ); ?>
				</div>
			</div>

			<div class="col-lg-7 ps-lg-0 align-self-stretch">
				<div class="quotes-info">
					<div class="text">
						<blockquote>
							<q><?php echo esc_html( $quote ); ?></q>
							<h5><?php echo esc_html( $source ); ?></h5>
							<p><?php echo esc_html( $source_title ); ?></p>
						</blockquote>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="spacer-120 spacer-xs-96"></div>

</div>
