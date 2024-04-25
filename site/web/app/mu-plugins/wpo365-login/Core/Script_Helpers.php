<?php

namespace Wpo\Core;

use \Wpo\Core\WordPress_Helpers;
use \Wpo\Services\Options_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Core\Script_Helpers')) {

    class Script_Helpers
    {

        /**
         * Helper to enqueue the pintra redirect script.
         * 
         * @since    8.6
         * 
         * @since   15.4    Added inline script to globally define isWpLogin as true if WP login is detected.
         * 
         * @return void
         */
        public static function enqueue_pintra_redirect()
        {
            if (Options_Service::get_global_boolean_var('use_no_teams_sso')) {
                wp_enqueue_script('pintraredirectjs', trailingslashit($GLOBALS['WPO_CONFIG']['plugin_url']) . 'apps/dist/pintra-redirect-wo-teams.js', array(), $GLOBALS['WPO_CONFIG']['version'], false);
            } else if (Options_Service::get_global_boolean_var('use_ms_teams_sso_v1')) {
                wp_enqueue_script('pintraredirectjs', trailingslashit($GLOBALS['WPO_CONFIG']['plugin_url']) . 'apps/dist/pintra-redirect-v1.js', array(), $GLOBALS['WPO_CONFIG']['version'], false);
            } else {
                wp_enqueue_script('pintraredirectjs', trailingslashit($GLOBALS['WPO_CONFIG']['plugin_url']) . 'apps/dist/pintra-redirect.js', array(), $GLOBALS['WPO_CONFIG']['version'], false);
            }

            if (class_exists('\Wpo\Core\Url_Helpers') && \Wpo\Core\Url_Helpers::is_wp_login()) {
                \wp_add_inline_script('pintraredirectjs', 'window.wpo365 = window.wpo365 || {}; window.wpo365.isWpLogin = true;', 'before');
            }
        }

        /**
         * Helper to enqueue the wizard script.
         * 
         * @since 8.6
         * 
         * @return void
         */
        public static function enqueue_wizard()
        {

            if (!(is_admin() || is_network_admin()) ||  !isset($_REQUEST['page']) || WordPress_Helpers::stripos($_REQUEST['page'], 'wpo365-wizard') === false) {
                return;
            }

            global $wp_roles;

            $extensions = array();

            // Bundles
            if (class_exists('\Wpo\Premium')) $extensions[] = 'wpo365LoginPremium';
            if (class_exists('\Wpo\Intranet')) $extensions[] = 'wpo365LoginIntranet';

            // Extensions
            if (class_exists('\Wpo\Apps')) $extensions[] = 'wpo365Apps';
            if (class_exists('\Wpo\Avatar')) $extensions[] = 'wpo365Avatar';
            if (class_exists('\Wpo\Custom_Fields')) $extensions[] = 'wpo365CustomFields';
            if (class_exists('\Wpo\Groups')) $extensions[] = 'wpo365Groups';
            if (class_exists('\Wpo\Plus')) $extensions[] = 'wpo365LoginPlus';
            if (class_exists('\Wpo\Professional')) $extensions[] = 'wpo365LoginProfessional';
            if (class_exists('\Wpo\Mail')) $extensions[] = 'wpo365Mail';
            if (class_exists('\Wpo\Roles_Access')) $extensions[] = 'wpo365RolesAccess';
            if (class_exists('\Wpo\Scim')) $extensions[] = 'wpo365Scim';

            // Plugins
            if (class_exists('\Wpo\Login')) $extensions[] = 'wpo365Login';
            if (class_exists('\Wpo\MsGraphMailer')) $extensions[] = 'wpo365MsGraphMailer';
            if (!empty(get_option('mail_integration_365_plugin_ops'))) $extensions[] = 'mailIntegration';

            $itthinx_groups = class_exists('\Wpo\Services\Mapped_Itthinx_Groups_Service') ? \Wpo\Services\Mapped_Itthinx_Groups_Service::get_groups_groups() : array();
            $post_types = get_post_types();

            $props = array(
                'adminUrl'           => get_site_url(null, '/wp-admin'),
                'availableGroups'    => json_encode($itthinx_groups),
                'availablePostTypes' => json_encode($post_types),
                'availableRoles'     => json_encode($wp_roles->roles),
                'extensions'         => $extensions,
                'nonce'              => wp_create_nonce('wpo365_fx_nonce'),
                'restNonce'          => wp_create_nonce('wp_rest'),
                'siteUrl'            => get_home_url(),
                'wpmu'               => is_multisite() ? (defined('WPO_MU_USE_SUBSITE_OPTIONS') && true === constant('WPO_MU_USE_SUBSITE_OPTIONS') ? 'wpmuDedicated' : 'wpmuShared') : 'wpmuNone',
            );

            wp_enqueue_script('wizardjs', trailingslashit($GLOBALS['WPO_CONFIG']['plugin_url']) . 'apps/dist/wizard.js', array(), $GLOBALS['WPO_CONFIG']['version'], true);

            \wp_add_inline_script('wizardjs', 'window.wpo365 = window.wpo365 || {}; window.wpo365.wizard = ' . json_encode(array(
                'nonce' => wp_create_nonce('wpo365_fx_nonce'),
                'wpAjaxAdminUrl' => admin_url() . 'admin-ajax.php',
                'props' => $props,
            )) . '; window.wpo365.blocks = ' . json_encode(array(
                'nonce' => \wp_create_nonce('wp_rest'),
                'apiUrl' => \trailingslashit($GLOBALS['WPO_CONFIG']['url_info']['wp_site_url']) . 'wp-json/wpo365/v1/graph',
            )), 'before');
        }

        /**
         * Helper to load the pintraredirectjs script asynchronously.
         * 
         * @since   18.0
         * 
         * @param mixed $tag 
         * @param mixed $handle 
         * @param mixed $src 
         * @return mixed 
         */
        public static function enqueue_script_asynchronously($tag, $handle, $src)
        {
            if ($handle === 'pintraredirectjs') {
                $tag = str_replace('></script>', ' async></script>', $tag);
            }

            return $tag;
        }

        /**
         * Helper to allow administrators to switch the CDN where to load the react/react-dom dependencies from.
         * This option was added after unpkg.com was poorly available on 28 Oct. 2022.
         * 
         * @since   20.2
         * 
         * @return  array   Returns an assoc array with react_url and react_dom_url.
         */
        public static function get_react_urls()
        {

            if (Options_Service::get_global_boolean_var('use_alternative_cdn')) {

                // Administrator can define his / her own URLs e.g. when self-hosting the react files
                if (defined('WPO_CDN') && is_array(constant('WPO_CDN'))) {
                    $react_url = !empty(constant('WPO_CDN')['react']) ? constant('WPO_CDN')['react'] : '';
                    $react_dom_url = !empty(constant('WPO_CDN')['react_dom']) ? constant('WPO_CDN')['react_dom'] : '';
                }

                // If not self-hosted then we take the react.js file from cdnjs.cloudflare.com instead
                if (empty($react_url) || !filter_var($react_url, FILTER_VALIDATE_URL)) {
                    $react_url = 'https://cdnjs.cloudflare.com/ajax/libs/react/16.14.0/umd/react.production.min.js';
                }

                // If not self-hosted then we take the react-dom.js file from cdnjs.cloudflare.com instead
                if (empty($react_dom_url) || !filter_var($react_dom_url, FILTER_VALIDATE_URL)) {
                    $react_dom_url = 'https://cdnjs.cloudflare.com/ajax/libs/react-dom/16.14.0/umd/react-dom.production.min.js';
                }
            }

            // If the administrator did not configure the use of an alternative CDN we take the react.js file from unpkg.com
            if (empty($react_url) || !filter_var($react_url, FILTER_VALIDATE_URL)) {
                $react_url = 'https://unpkg.com/react@16/umd/react.production.min.js';
            }

            // If the administrator did not configure the use of an alternative CDN we take the react-dom.js file from unpkg.com
            if (empty($react_dom_url) || !filter_var($react_dom_url, FILTER_VALIDATE_URL)) {
                $react_dom_url = 'https://unpkg.com/react-dom@16/umd/react-dom.production.min.js';
            }

            return array(
                'react_url' => $react_url,
                'react_dom_url' => $react_dom_url
            );
        }
    }
}
