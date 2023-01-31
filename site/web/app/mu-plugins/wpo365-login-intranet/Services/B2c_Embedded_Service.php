<?php

namespace Wpo\Services;

use \Wpo\Core\Extensions_Helpers;
use \Wpo\Core\WordPress_Helpers;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Services\B2c_Embedded_Service.php')) {

    class B2c_Embedded_Service
    {
        /**
         * Helper method to ensure that short code for the embedded Azure AD B2C login page.
         * 
         * @since   20.x
         */
        public static function ensure_b2c_embedded_short_code()
        {
            add_shortcode('wpo365-b2c-embedded-sc', '\Wpo\Services\B2c_Embedded_Service::add_b2c_embedded_shortcode');
        }

        /**
         * 
         * @param   array   $atts 
         * @param   mixed   $content 
         * @param   string  $tag 
         * 
         * @return  mixed 
         */
        public static function add_b2c_embedded_shortcode($atts = array(), $content = null, $tag = '')
        {
            $atts = array_change_key_case((array)$atts, CASE_LOWER);

            $b2c_policy = !empty($atts['b2c_policy']) && Options_Service::get_global_boolean_var('b2c_allow_multiple_policies') ? $atts['b2c_policy'] : Options_Service::get_global_string_var('b2c_policy_name');
            $wait = !empty($atts['wait']) ? intval($atts['wait']) : 500;
            $redirect_to = !empty($atts['redirect_to']) ? $atts['redirect_to'] : '';

            ob_start();
            include(Extensions_Helpers::get_active_extension_dir(array('wpo365-login-professional/wpo365-login.php', 'wpo365-login-premium/wpo365-login.php', 'wpo365-login-intranet/wpo365-login.php')) . '/templates/b2c-embedded-login.php');
            return ob_get_clean();
        }
    }
}
