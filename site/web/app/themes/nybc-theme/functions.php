<?php
/**
 *
 * NYBC functions and definitions
 *
 * @file
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Constants
 */
define( 'NYBC_HOME_URI', home_url( '/' ) );
define( 'NYBC_THEME_URI', get_template_directory_uri() );
define( 'NYBC_ASSETS_URI', NYBC_THEME_URI . '/dist' );
define( 'NYBC_LIB_URI', NYBC_THEME_URI . '/lib' );
define( 'NYBC_IMG_URI', NYBC_THEME_URI . '/img' );
define( 'NYBC_THEME_DIR', get_template_directory() );
define( 'NYBC_SCRIPT_VER', '1.0.0' );

define( 'CONCATENATE_SCRIPTS', false );

function acf_filter_rest_api_preload_paths( $preload_paths ) {
    global $post;
    $rest_path    = rest_get_route_for_post( $post );
    $remove_paths = array(
        add_query_arg( 'context', 'edit', $rest_path ),
        sprintf( '%s/autosaves?context=edit', $rest_path ),
    );

    return array_filter(
        $preload_paths,
        function( $url ) use ( $remove_paths ) {
            return ! in_array( $url, $remove_paths, true );
        }
    );
}
add_filter( 'block_editor_rest_api_preload_paths', 'acf_filter_rest_api_preload_paths', 10, 1 );

/**
 * Init Requirements
 */
require_once __DIR__ . '/inc/class-nybc-acf-icon-picker.php';
require_once __DIR__ . '/inc/class-nybc-init.php';
require_once __DIR__ . '/inc/class-nybc-helpers.php';
require_once __DIR__ . '/inc/class-nybc-staff-member.php';
require_once __DIR__ . '/inc/class-nybc-news-article.php';
require_once __DIR__ . '/inc/class-nybc-story.php';
require_once __DIR__ . '/inc/class-nybc-blocks.php';
require_once __DIR__ . '/inc/class-nybc-block-patterns.php';
require_once __DIR__ . '/inc/class-nybc-articulate.php';
/* Disable Load CSV Button for tablepress */
//require_once __DIR__ . '/inc/class-nybc-table.php';
require_once __DIR__ . '/inc/class-nybc-distributor.php';




