<?php
/**
 * Configuration overrides for WP_ENV === 'staging'
 */

use Roots\WPConfig\Config;

/**
 * You should try to keep staging as close to production as possible. However,
 * should you need to, you can always override production configuration values
 * with `Config::define`.
 *
 * Example: `Config::define('WP_DEBUG', true);`
 * Example: `Config::define('DISALLOW_FILE_MODS', false);`
 */

// Enable plugin and theme updates and installation from the admin
Config::define( 'FS_METHOD', 'direct' );
Config::define('DISALLOW_FILE_MODS', true);

define('MICROSOFT_AZURE_ACCOUNT_NAME', env('MICROSOFT_AZURE_ACCOUNT_NAME'));
define('MICROSOFT_AZURE_ACCOUNT_KEY', env('MICROSOFT_AZURE_ACCOUNT_KEY'));
define('MICROSOFT_AZURE_USE_FOR_DEFAULT_UPLOAD', 1);
define('MICROSOFT_AZURE_CONTAINER', env('MICROSOFT_AZURE_STORAGES_CONTAINER'));
