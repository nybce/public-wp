<?php

/**
 *  Plugin Name: WPO365 | INTRANET
 *  Plugin URI: https://www.wpo365.com/downloads/wordpress-office-365-login-intranet/
 *  Description: Extends WPO365 | LOGIN and offers the deepest integration with the Microsoft Office 365 / Azure cloud, incl. apps for Power BI, SharePoint Online, Microsoft Graph and Yammer and support for Azure AD user provisioning (SCIM).
 *  Version: 18.0
 *  Author: support@wpo365.com
 *  Author URI: https://www.wpo365.com
 *  License: See license.txt
 */

namespace Wpo;

require __DIR__ . '/vendor/autoload.php';

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Intranet')) {

    class Intranet
    {

        public function __construct()
        {
            // Show admin notification when BASIC edition is not installed
            add_action('admin_notices', array($this, 'ensure_wpo365_login'), 10, 0);
            add_action('network_admin_notices', array($this, 'ensure_wpo365_login'), 10, 0);
            add_action('plugins_loaded', function () {
                $this->load_gutenberg_blocks();
            });
        }

        public function ensure_wpo365_login()
        {
            $plugin_exists = \file_exists(dirname(__DIR__) . '/wpo365-login');
            $version_exists = \class_exists('\Wpo\Core\Version') && isset(\Wpo\Core\Version::$current) && \Wpo\Core\Version::$current >= 13;

            // Required version installed and activated
            if ($version_exists) {
                return;
            }

            // Required plugin not installed
            if (!$plugin_exists) {
                $install_url = wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'install-plugin',
                            'plugin' => 'wpo365-login',
                            'from'   => 'plugins',
                        ),
                        self_admin_url('update.php')
                    ),
                    'install-plugin_wpo365-login'
                );
                echo '<div class="notice notice-error" style="margin-left: 2px;"><p>'
                    . sprintf(__('The %s plugin requires the latest version of %s to be installed and activated.', 'wpo365-login'), '<strong>WPO365 | INTRANET</strong>', '<strong>WPO365 | LOGIN (free)</strong>')
                    . '</p><p>'
                    . '<a class="button button-primary" href="' . esc_url($install_url) . '">' . __('Install plugin', 'wpo365-login') . '</a>.'
                    . '</p></div>';
                return;
            }

            // Required plubin installed but must either be activated or upgraded 
            $activate_url = add_query_arg(
                array(
                    '_wpnonce' => wp_create_nonce('activate-plugin_wpo365-login/wpo365-login.php'),
                    'action'   => 'activate',
                    'plugin'   => 'wpo365-login/wpo365-login.php',
                ),
                network_admin_url('plugins.php')
            );

            if (is_network_admin()) {
                $activate_url = add_query_arg(array('networkwide' => 1), $activate_url);
            }

            $update_url = wp_nonce_url(
                self_admin_url('update.php?action=upgrade-plugin&plugin=') . 'wpo365-login/wpo365-login.php',
                'upgrade-plugin_wpo365-login/wpo365-login.php'
            );

            echo '<div class="notice notice-error" style="margin-left: 2px;"><p>'
                . sprintf(__('The %s plugin requires the latest version of %s to be installed and activated.', 'wpo365-login'), '<strong>WPO365 | INTRANET</strong>', '<strong>WPO365 | LOGIN (free)</strong>')
                . '</p><p>'
                . '<a class="button button-primary" href="' . esc_url($activate_url) . '">' . __('Activate plugin', 'wpo365-login') . '</a>&nbsp;'
                . '<a class="button button-primary" href="' . esc_url($update_url) . '">' . __('Update plugin', 'wpo365-login') . '</a>'
                . '</p></div>';
        }

        /**
         * @since 13.0
         * 
         * To load the Gutenberg M365 blocks.
         */
        private function load_gutenberg_blocks()
        {
            $apps = array(
                'docs' => array(
                    'edition' => 'premium',
                    'load_front_end_assets' => true,
                )
            );

            if (class_exists('\Wpo\Services\Options_Service') && \Wpo\Services\Options_Service::get_global_boolean_var('enable_audiences')) {
                $apps['aud'] = array(
                    'edition' => 'premium',
                    'load_front_end_assets' => false,
                );
            }

            $plugins_dir = __DIR__;
            $plugins_url = \plugins_url() . '/' . basename(__DIR__);

            if (\class_exists('\Wpo\Blocks\Loader')) {

                foreach ($apps as $app => $settings) {
                    new \Wpo\Blocks\Loader($app, $settings['edition'], $plugins_dir, $plugins_url, $settings['load_front_end_assets']);
                }
            }
        }
    }
}

$wpo365_intranet = new Intranet();
