<?php

namespace PublishPress\Capabilities;

/*
 * PublishPress Capabilities Pro
 * 
 * Process updates to Pro plugin settings
 * 
 */

class Pro_Settings_Handler {
    public function __construct() {
        $this->handleUpdate();
    }

    public function handleUpdate() {
        if (check_admin_referer('pp-capabilities-settings') && current_user_can('manage_capabilities')) {
            if (!empty($_POST['all_options_pro'])) {
                foreach (array_map('sanitize_key', explode(',', sanitize_text_field($_POST['all_options_pro']))) as $option_name) {
                    foreach (['cme_', 'capsman', 'pp_capabilities'] as $prefix) {
                        if (0 === strpos($option_name, $prefix)) {
                            $value = isset($_POST[$option_name]) ? sanitize_text_field($_POST[$option_name]) : '';
                
                            if (!is_array($value)) {
                                $value = trim($value);
                            }
                            
                            update_option($option_name, $value);
                        }
                    }
                }
            }
        }
    }
}
