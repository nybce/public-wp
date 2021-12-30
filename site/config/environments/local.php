<?php
/**
 * Configuration overrides for WP_ENV === 'local'
 */

use Roots\WPConfig\Config;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', true);
Config::define('SCRIPT_DEBUG', true);

ini_set('display_errors', 1);

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);

define('MICROSOFT_AZURE_USE_FOR_DEFAULT_UPLOAD', 0);
