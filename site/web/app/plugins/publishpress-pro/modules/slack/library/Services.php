<?php
/**
 * @package     PublishPress\Slack
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Slack;

use Pimple\Container as Pimple;
use Pimple\ServiceProviderInterface;
use PP_Slack;
use PublishPress\EDD_License\Core\Container as EDDContainer;
use PublishPress\EDD_License\Core\Services as EDDServices;
use PublishPress\EDD_License\Core\ServicesConfig as EDDServicesConfig;

defined('ABSPATH') or die('No direct script access allowed.');

/**
 * Class Services
 */
class Services implements ServiceProviderInterface
{
    /**
     * @since 1.2.3
     * @var PP_Slack
     */
    protected $module;

    /**
     * Services constructor.
     *
     * @param PP_Slack $module
     * @since 1.2.3
     *
     */
    public function __construct(PP_Slack $module)
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
     * @since 1.2.3
     *
     */
    public function register(Pimple $container)
    {
        $container['module'] = function ($c) {
            return $this->module;
        };
    }
}
