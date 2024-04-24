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

$block_id = 'zip-code-search-with-cta-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title       = get_field( 'title' );
$input_label       = get_field( 'input_label' );
$lnk               = get_field( 'link' );
$description_title = get_field( 'description_title' );
$description       = get_field( 'description' );
$title_cta         = get_field( 'title_cta' );
$lnk_cta           = get_field( 'link_cta' );
$description_cta   = get_field( 'description_cta' );
?>
<div class="section promo <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div class="container container-lg">
		<div class="row">
			<div class="col-lg-8">

				<div class="promo-wrapper type-2">
					<form action="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '' ); ?>" target="_blank">
						<div class="promo-item">
							<div class="h5 title fw-800 light"><?php echo esc_html( $block_title ); ?></div>
							<div class="spacer-16"></div>
							<div class="text text-30"><?php echo esc_html( $input_label ); ?></div>
							<div class="spacer-16"></div>
							<input type="text" class="input" name="zipcode" required placeholder="">
							<button type="submit"
								class="btn btn-small btn-secondary"><?php echo esc_html( ! empty( $lnk ) ? $lnk['title'] : '' ); ?></button>
						</div>
						<?php if ( ! empty( $description_title ) || ! empty( $description ) ) { ?>
						<div class="promo-item">
							<div class="h5 title fw-800 light"><?php echo esc_html( $description_title ); ?></div>
							<div class="spacer-8"></div>
							<div class="text text-30"><?php echo esc_html( $description ); ?></div>
						</div>
						<?php } ?>
					</form>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="promo-cta type-2">
					<div class="h4 title fw-800 light"><?php echo esc_html( $title_cta ); ?></div>
					<div class="spacer-8"></div>
					<div class="text-lg text-30"><?php echo wp_kses_post( $description_cta ); ?></div>
					<?php if ( ! empty( $lnk_cta ) ) { ?>
					<div class="spacer-24"></div>
					<a class="btn btn-small btn-secondary" target="<?php echo esc_attr( $lnk_cta['target'] ); ?>"
						href="<?php echo esc_url( $lnk_cta['url'] ); ?>"><?php echo esc_html( $lnk_cta['title'] ); ?></a>
					<?php } ?>
				</div>

			</div>
		</div>
	</div>
</div>
<div class="spacer-120"></div>