<?php
/**
 * The template for displaying the footer
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
?>
<footer>
	<?php get_template_part( 'template-parts/footer/footer' ); ?>
</footer>

</div>
	<?php wp_footer(); ?>
	<?php
	if ( ! is_front_page() && is_page() && isset( $post ) && ! $post->post_parent && get_field( 'enable_promo_sticky_modal_cta', 'options' ) ) {
		get_template_part( 'template-parts/promo-sticky-modal-cta' );
	}
	?>
</body>

</html>
