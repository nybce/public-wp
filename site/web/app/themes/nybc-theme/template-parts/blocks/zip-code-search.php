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

$block_id = 'zip-code-search-' . $block['id'];
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

$news_page    = get_field( 'news_page', 'options' );
$stories_page = get_field( 'stories_page', 'options' );
?>
<div class="promo-wrapper <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<?php if ( $news_page && ! is_page( $news_page ) && $stories_page && ! is_page( $stories_page ) ) { ?>
		<div class="decor-promo mobile" data-rellax-speed="-1" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/wave.svg');"></div>
	<?php } ?>
	<div class="promo-item">
		<form action="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '' ); ?>" target="_blank">
			<div class="h5 title fw-800"><?php echo esc_html( $block_title ); ?></div>
			<div class="spacer-16"></div>
			<div class="text text-20"><?php echo esc_html( $input_label ); ?></div>
			<div class="spacer-16"></div>
			<input type="text" name="zipcode" class="input" required placeholder="">
			<div class="spacer-16"></div>
			<button type="submit"
					class="btn btn-primary"><?php echo esc_html( ! empty( $lnk ) ? $lnk['title'] : '' ); ?></button>
		</form>
	</div>
	<?php if ( ! empty( $description_title ) || ! empty( $description ) ) { ?>
		<div class="spacer-48"></div>
		<div class="promo-item">
			<div class="h5 title fw-800"><?php echo esc_html( $description_title ); ?></div>
			<div class="spacer-16"></div>
			<div class="text text-20"><?php echo esc_html( $description ); ?></div>
		</div>
	<?php } ?>
</div>
