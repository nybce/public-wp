<?php
/**
 * @package     WPPF2
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace WPPF2\Plugin;

use WPPF2\WP\HooksHandlerInterface;
use WPPF2\WP\PluginsHandlerInterface;
use WPPF2\WP\TranslatorInterface;

interface PluginInitializerInterface
{
    /**
     * PluginInitializerInterface constructor.
     *
     * @param HooksHandlerInterface   $hooksHandler
     * @param PluginsHandlerInterface $pluginsHandler
     * @param TranslatorInterface     $textDomainLoader
     * @param string                  $pluginDirPath
     */
    public function __construct(
        HooksHandlerInterface $hooksHandler,
        PluginsHandlerInterface $pluginsHandler,
        TranslatorInterface $textDomainLoader,
        $pluginDirPath
    );

    /**
     * @return void
     */
    public function init();
}
