<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package NYBC_Enterprise
 */

global $post;
?>
<footer>
	<?php get_template_part( 'template-parts/footer/footer' ); ?>
</footer>

</div>
	<?php wp_footer(); ?>
	<?php
	if ( ! is_front_page() && is_page() && isset( $post ) && ! $post->post_parent ) {
		get_template_part( 'template-parts/promo-sticky-modal-cta' );
	}
	?>
</body>

</html>
