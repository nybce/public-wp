<?php
/**
 * @package     PublishPressPro
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPressPro;

use PublishPressPro\HooksAbstract as PPProHooksAbstract;
use WPPF2\Plugin\PluginInitializerInterface;
use WPPF2\WP\HooksAbstract as WPHooksAbstract;
use WPPF2\WP\HooksHandlerInterface;
use WPPF2\WP\PluginsHandlerInterface;
use WPPF2\WP\TranslatorInterface;

class PluginInitializer implements PluginInitializerInterface
{
    const FREE_PLUGIN_NAME = 'publishpress';

    /**
     * @var HooksHandlerInterface
     */
    private $hooksHandler;

    /**
     * @var PluginsHandlerInterface
     */
    private $pluginsHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $modulesDirPath;

    /**
     * @inheritDoc
     */
    public function __construct(
        HooksHandlerInterface $hooksHandler,
        PluginsHandlerInterface $pluginsHandler,
        TranslatorInterface $translator,
        $modulesDirPath
    ) {
        $this->hooksHandler   = $hooksHandler;
        $this->pluginsHandler = $pluginsHandler;
        $this->translator     = $translator;
        $this->modulesDirPath = $modulesDirPath;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->setHooks();
    }

    private function setHooks()
    {
        $this->hooksHandler->addAction(WPHooksAbstract::ACTION_INIT, [$this, 'deactivateFreePluginIfActive']);
        $this->hooksHandler->addAction(WPHooksAbstract::ACTION_PLUGINS_LOADED, [$this, 'loadTextDomain']);
        $this->hooksHandler->addFilter(PPProHooksAbstract::FILTER_MODULES_DIRS, [$this, 'filterModulesDirs']);
    }

    public function deactivateFreePluginIfActive()
    {
        $this->pluginsHandler->deactivatePluginIfActive(self::FREE_PLUGIN_NAME);
    }

    public function loadTextDomain()
    {
        $this->translator->loadTextDomain();
    }

    private function isElementorInstalled()
    {
        return defined('ELEMENTOR_VERSION') || defined('ELEMENTOR_PRO_VERSION');
    }

    /**
     * @param array $dirs
     *
     * @return array
     */
    public function filterModulesDirs($dirs)
    {
        $modulesParentDir = rtrim($this->modulesDirPath, '/\\');

        $dirs['prosettings']        = $modulesParentDir;
        $dirs['slack']              = $modulesParentDir;
        $dirs['reminders']          = $modulesParentDir;
        $dirs['notifynetworkadmin'] = $modulesParentDir;

        if ($this->isElementorInstalled()) {
            $dirs['elementor'] = $modulesParentDir;
        }

        return $dirs;
    }
}
