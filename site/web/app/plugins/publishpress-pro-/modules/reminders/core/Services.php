<?php
/**
 * @package     PublishPress\Reminders
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.3
 */

namespace PublishPress\Addon\Reminders;

use Pimple\Container as Pimple;
use Pimple\ServiceProviderInterface;
use PP_Reminders;
use PublishPress\Addon\Reminders\Util\Time;
use PublishPress\EDD_License\Core\Container as EDDContainer;
use PublishPress\EDD_License\Core\Services as EDDServices;
use PublishPress\EDD_License\Core\ServicesConfig as EDDServicesConfig;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

defined('ABSPATH') or die('No direct script access allowed.');

/**
 * Class Services
 */
class Services implements ServiceProviderInterface
{
    /**
     * @since 1.2.3
     * @var PP_Reminders
     */
    protected $module;

    /**
     * Services constructor.
     *
     * @param PP_Reminders $module
     *
     * @since 1.2.3
     */
    public function __construct(PP_Reminders $module)
    {
        $this->module = $module;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Pimple $container A container instance
     *
     * @since 1.2.3
     */
    public function register(Pimple $container)
    {
        $container['twig_loader'] = function ($c) {
            $loader = new Twig_Loader_Filesystem($c['twig_path']);

            return $loader;
        };

        $container['twig'] = function ($c) {
            $twig = new Twig_Environment($c['twig_loader'], []);

            $twig->addFunction($c['twig_function_checked']);
            $twig->addFunction($c['twig_function_selected']);

            return $twig;
        };

        $container['module'] = function ($c) {
            return $this->module;
        };

        $container['twig_path'] = function ($c) {
            return __DIR__ . '/../twig';
        };

        $container['twig_function_checked'] = function ($c) {
            return new Twig_SimpleFunction('checked', function ($checked, $current = true, $echo = true) {
                return checked($checked, $current, $echo);
            });
        };

        $container['twig_function_selected'] = function ($c) {
            return new Twig_SimpleFunction('selected', function ($selected, $current = true, $echo = true) {
                return selected($selected, $current, $echo);
            });
        };

        $container['util_time'] = function ($c) {
            return new Time();
        };
    }
}
