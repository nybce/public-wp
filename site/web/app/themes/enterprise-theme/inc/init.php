<?php
/**
 * NYBC Enterprise Theme Init
 *
 * @package NYBC_Enterprise
 */

/**
 * Set up the baseline styles.
 */
function enterprise_sass_styles() {
	// Normalize is loaded in enterprise-theme and both are imported into the style.css via Sass.
	wp_register_style( 'enterprise_sass', get_template_directory_uri() . '/dist/style.min.css', array(), '1.0.0', 'all' );
	wp_enqueue_style( 'enterprise_sass' ); // Enqueue it!
}

add_action( 'wp_enqueue_scripts', 'enterprise_sass_styles' ); // Add Theme Stylesheet.

/**
 * Set up the baseline scripts.
 */
function enterprise_header_scripts() {
	if ( 'wp-login.php' !== $GLOBALS['pagenow'] && ! is_admin() ) {

		// Custom scripts.
		wp_register_script( 'enterprise_scripts', get_template_directory_uri() . '/dist/main.bundle.js', array( 'jquery' ), '1.0.0', false );

		// Enqueue it!
		wp_enqueue_script( array( 'enterprise_scripts' ) );

	}
}
add_action( 'init', 'enterprise_header_scripts' ); // Add Custom Scripts to wp_head.
