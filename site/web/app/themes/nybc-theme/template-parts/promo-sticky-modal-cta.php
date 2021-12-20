<?php
/**
 * Promo Sticky Modal CTA
 *
 * @package NYBC
 */

$block_fields = get_field( 'promo_sticky_modal_cta', 'options' );
$block_title  = $block_fields['title'];
$body         = $block_fields['body'];

$lnk = $block_fields['link'];
?>
<div class="promo-cta-modal">
<div class="promo-cta">
	<div class="h4 title fw-800 light"><?php echo esc_html( $block_title ); ?></div>
	<div class="spacer-8"></div>
	<div class="text-lg text-30"><?php echo esc_html( $body ); ?></div>
	<?php if ( ! empty( $lnk ) ) { ?>
		<div class="spacer-24"></div>
		<a class="btn btn-small btn-secondary" target="<?php echo esc_attr( $lnk['target'] ); ?>" href="<?php echo esc_url( $lnk['url'] ); ?>"><?php echo esc_html( $lnk['title'] ); ?></a>
	<?php } ?>
</div>

<div class="btn-close"></div>
</div>
