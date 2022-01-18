<?php
/**
 * Plugin Name: PublishPress Pro
 * Plugin URI: https://publishpress.com/
 * Description: PublishPress helps you plan and publish content with WordPress. Features include a content calendar, notifications, and custom statuses.
 * Author: PublishPress
 * Author URI: https://publishpress.com
 * Version: 3.6.3
 * Text Domain: publishpress-pro
 * Domain Path: /languages
 *
 * Copyright (c) 2020 PublishPress
 *
 * ------------------------------------------------------------------------------
 * Based on Edit Flow
 * Author: Daniel Bachhuber, Scott Bressler, Mohammad Jangda, Automattic, and
 * others
 * Copyright (c) 2009-2016 Mohammad Jangda, Daniel Bachhuber, et al.
 * ------------------------------------------------------------------------------
 *
 * GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     PublishPress
 * @category    Core
 * @author      PublishPress
 * @copyright   Copyright (C) 2020 PublishPress. All rights reserved.
 */

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

function ppProGetPluginsRelativePath($pluginName)
{
    $pluginsPath        = dirname(__DIR__);
    $pluginsDirectories = @scandir($pluginsPath);
    $expectedFilename   = $pluginName . '.php';

    if (empty($pluginsDirectories)) {
        return false;
    }

    foreach ($pluginsDirectories as $dir) {
        if ('.' !== $dir && '..' !== $dir && is_dir($pluginsPath . '/' . $dir)) {
            $files = @scandir($pluginsPath . '/' . $dir);

            if (!empty($files) && in_array($expectedFilename, $files)) {
                return $pluginsPath . '/' . $dir . '/' . $expectedFilename;
            }
        }
    }

    return false;
}

function ppProIsPluginActivated($pluginName)
{
    // First we try to find the plugin in the expected folder.
    $expectedPath = dirname(__DIR__) . '/' . $pluginName . '/' . $pluginName . '.php';
    if (file_exists($expectedPath)) {
        $pluginPath = $expectedPath;
    } else {
        $pluginPath = ppProGetPluginsRelativePath($pluginName);
    }


    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    $pluginBaseName = plugin_basename($pluginPath);

    return is_plugin_active($pluginBaseName);
}

$pluginPath = ppProIsPluginActivated('publishpress-reminders');
if ($pluginPath !== false) {
    deactivate_plugins([$pluginPath]);
}

$pluginPath = ppProIsPluginActivated('publishpress-slack');
if ($pluginPath !== false) {
    deactivate_plugins([$pluginPath]);
}

$hasLegacyPluginActive = ppProIsPluginActivated('publishpress');

if (!defined('PUBLISHPRESS_PRO_LOADED') && !$hasLegacyPluginActive) {
    require_once __DIR__ . '/includes.php';
}

if ($hasLegacyPluginActive && is_admin()) {
    global $pagenow;

    if ('plugins.php' === $pagenow) {
        add_action(
            'admin_notices',
            function () {
                $msg = sprintf(
                    '<strong>%s:</strong> %s',
                    __('Warning', 'publishpress-pro'),
                    __('Please, deactivate and remove PublishPress before using PublishPress Pro.', 'publishpress-pro')
                );

                echo "<div class='notice notice-error is-dismissible' style='color:black'><p>" . $msg . '</p></div>';
            },
            5
        );
    }
}
