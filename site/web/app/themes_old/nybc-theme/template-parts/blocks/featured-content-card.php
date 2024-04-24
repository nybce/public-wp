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

$block_id = 'featured-content-card-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= $block['className'];
}

$block_title = get_field( 'title' );
$lnk         = get_field( 'link' );
$image       = get_field( 'image' );
$tagline     = get_field( 'tagline' );
$text        = get_field( 'text' );
?>

<a class="news-item <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $block_id ); ?>" target="<?php echo esc_attr( ! empty( $lnk ) ? $lnk['target'] : '' ); ?>" href="<?php echo esc_url( ! empty( $lnk ) ? $lnk['url'] : '#' ); ?>">
	<?php
	if ( ! empty( $image ) ) {
		?>
		<div class="news-img">
			<?php NYBC_Helpers::picture( $image, '519x292' ); ?>
		</div>
	<?php } ?>
	<div class="border-top"></div>

	<div class="news-info">
		<ul class="tags-list">
			<li class="tag-main"><?php echo esc_html( $tagline ); ?></li>
		</ul>

		<div class="spacer-16"></div>

		<div class="h5 title fw-800"><?php echo esc_html( $block_title ); ?></div>

		<?php if ( ! empty( $text ) ) { ?>
			<div class="spacer-16"></div>

			<div class="text text-20"><?php echo esc_html( $text ); ?></div>
		<?php } ?>
	</div>
</a>
<div class="spacer-120 spacer-xs-64"></div>
