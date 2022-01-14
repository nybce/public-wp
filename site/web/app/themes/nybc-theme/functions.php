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
require_once __DIR__ . '/inc/class-nybc-table.php';




