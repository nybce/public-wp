<?php
/**
 *
 * NYBC functions and definitions
 *
 * @file
 *
 * @package NYBC
 */

/**
 * Constants
 */
define( 'NYBC_HOME_URI', home_url( '/' ) );
define( 'NYBC_THEME_URI', get_template_directory_uri() );
define( 'NYBC_ASSETS_URI', NYBC_THEME_URI . '/dist' );
define( 'NYBC_THEME_DIR', get_template_directory() );
define( 'NYBC_SCRIPT_VER', '1.0.0' );


/**
 * Init Requirements
 */
require_once __DIR__ . '/inc/class-nybc-helpers.php';
require_once __DIR__ . '/inc/class-nybc-blocks.php';



