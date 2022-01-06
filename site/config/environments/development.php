<?php
/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;
use function Env\env;

Config::define('SAVEQUERIES', false);
Config::define('WP_DEBUG', false);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('SCRIPT_DEBUG', false);

ini_set('display_errors', 0);

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', true);

define('MICROSOFT_AZURE_ACCOUNT_NAME', env('MICROSOFT_AZURE_ACCOUNT_NAME'));
define('MICROSOFT_AZURE_ACCOUNT_KEY', env('MICROSOFT_AZURE_ACCOUNT_KEY'));
define('MICROSOFT_AZURE_USE_FOR_DEFAULT_UPLOAD', 1);

