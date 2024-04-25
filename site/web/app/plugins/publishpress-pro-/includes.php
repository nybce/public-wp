<?php
/**
 * @package PublishPressPro
 * @author  PublishPress
 *
 * Copyright (c) 2020 PublishPress
 *
 * ------------------------------------------------------------------------------
 * Based on Edit Flow
 * Author: Daniel Bachhuber, Scott Bressler, Mohammad Jangda, Automattic, and
 * others
 * Copyright (c ) 2009-2016 Mohammad Jangda, Daniel Bachhuber, et al.
 * ------------------------------------------------------------------------------
 *
 * This file is part of PublishPress
 *
 * PublishPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option ) any later version.
 *
 * PublishPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PublishPress.  If not, see <http://www.gnu.org/licenses/>.
 */

use PublishPressPro\Factory;
use WPPF2\Plugin\ServicesAbstract;

if (!defined('PUBLISHPRESS_PRO_LOADED')) {
    define('PUBLISHPRESS_PRO_VERSION', '3.6.3');

    define('PUBLISHPRESS_PRO_DIR_PATH', plugin_dir_path(__FILE__));

    define('PUBLISHPRESS_PRO_PLUGIN_URL', plugins_url('/', __FILE__));

    define('PUBLISHPRESS_PRO_ITEM_ID', 49742);

    define('PUBLISHPRESS_SKIP_VERSION_NOTICES', true);

    // Composer's autoload.
    $autoloadPath = PUBLISHPRESS_PRO_DIR_PATH . '/vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
    }

    // Initialize the free plugin.
    if (defined('PUBLISHPRESS_FREE_PLUGIN_PATH')) {
        require_once PUBLISHPRESS_FREE_PLUGIN_PATH . '/publishpress.php';
    } else {
        require_once PUBLISHPRESS_PRO_DIR_PATH . '/vendor/publishpress/publishpress/publishpress.php';
    }

    // Initialize the framework
    require_once PUBLISHPRESS_PRO_DIR_PATH . '/lib/wppf2/includes.php';

    // Initialize the Slack module - migrated from the Slack plugin.
    require_once PUBLISHPRESS_PRO_DIR_PATH . '/modules/slack/includes.php';

    // Initialize the Reminders module - migrated from the Reminders plugin.
    require_once PUBLISHPRESS_PRO_DIR_PATH . '/modules/reminders/includes.php';

    $container = Factory::getContainer();

    $pluginInitializer = $container->get(ServicesAbstract::PLUGIN_INITIALIZER);
    $pluginInitializer->init();

    define('PUBLISHPRESS_PRO_LOADED', 1);
}
