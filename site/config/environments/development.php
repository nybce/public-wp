<?php
/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;
use function Env\env;

Config::define('SAVEQUERIES', false);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_LOG', true);
Config::define('WP_DEBUG_DISPLAY', true);
Config::define('SCRIPT_DEBUG', false);

ini_set('display_errors', 1);

// Enable plugin and theme updates and installation from the admin
Config::define( 'FS_METHOD', 'direct' );
Config::define('DISALLOW_FILE_MODS', true);

define('MICROSOFT_AZURE_ACCOUNT_NAME', env('MICROSOFT_AZURE_ACCOUNT_NAME'));
define('MICROSOFT_AZURE_ACCOUNT_KEY', env('MICROSOFT_AZURE_ACCOUNT_KEY'));
define('MICROSOFT_AZURE_USE_FOR_DEFAULT_UPLOAD', 1);

