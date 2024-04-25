<?php
/**
 * @package PublishPress
 * @author  PublishPress
 *
 * Copyright (c) 2018 PublishPress
 *
 * ------------------------------------------------------------------------------
 * Based on Edit Flow
 * Author: Daniel Bachhuber, Scott Bressler, Mohammad Jangda, Automattic, and
 * others
 * Copyright (c) 2009-2016 Mohammad Jangda, Daniel Bachhuber, et al.
 * ------------------------------------------------------------------------------
 *
 * This file is part of PublishPress
 *
 * PublishPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PublishPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PublishPress.  If not, see <http://www.gnu.org/licenses/>.
 */


if (!class_exists('PP_Reminders')) {
    /**
     * class PP_Reminders
     */
    class PP_Reminders extends PP_Module
    {
        public $module_name = 'reminders';

        public $module;

        /**
         * Flag to assist conditional loading
         */
        private $twig_configured = false;

        /**
         * Construct the PP_Reminders class
         */
        public function __construct()
        {
            $this->twigPath = dirname(dirname(__DIR__)) . '/twig';

            $this->module_url = $this->get_module_url(__FILE__);

            // Register the module with PublishPress
            $args = [
                'title'             => __('Reminders', 'publishpress-reminders'),
                'module_url'        => $this->module_url,
                'icon_class'        => 'dashicons dashicons-feedback',
                'slug'              => 'reminders',
                'default_options'   => [
                    'enabled'                          => 'on',
                    'license_key'                      => '',
                    'license_status'                   => '',
                    'before_publishing_check_interval' => 'every_30_min',
                ],
                'configure_page_cb' => 'print_configure_view',
                'options_page'      => true,
            ];

            // Apply a filter to the default options
            $args['default_options'] = apply_filters('pp_reminders_default_options', $args['default_options']);

            $this->module = PublishPress()->register_module($this->module_name, $args);

            parent::__construct();
        }

        protected function configure_twig()
        {
            if ($this->twig_configured) {
                return;
            }

            $function = new Twig_SimpleFunction('settings_fields', function () {
                return settings_fields($this->module->options_group_name);
            });
            $this->twig->addFunction($function);

            $function = new Twig_SimpleFunction('nonce_field', function ($context) {
                return wp_nonce_field($context);
            });
            $this->twig->addFunction($function);

            $function = new Twig_SimpleFunction('submit_button', function () {
                return submit_button();
            });
            $this->twig->addFunction($function);

            $function = new Twig_SimpleFunction('do_settings_sections', function ($section) {
                return do_settings_sections($section);
            });
            $this->twig->addFunction($function);

            $this->twig_configured = true;
        }

        /**
         * Initialize the module. Conditionally loads if the module is enabled
         */
        public function init()
        {
            add_action('admin_init', [$this, 'register_settings']);

            add_action('admin_enqueue_scripts', [$this, 'add_admin_scripts']);

            add_filter('publishpress_notif_workflow_steps_event', [$this, 'addWorkflowStepEvent']);

            // Make sure we have the "before publishing" job running
            add_action('publishpress_workflow_steps_loaded', [$this, 'checkBeforePublishingCronJob']);

            add_filter('cron_schedules', [$this, 'addCronSchedules']);

            add_filter('publishpress_notif_shortcode_post_data', [$this, 'shortcodePostMetaData'], 10, 4);
        }

        /**
         * Load default editorial metadata the first time the module is loaded
         *
         * @since 0.7
         */
        public function install()
        {

        }

        /**
         * Upgrade our data in case we need to
         *
         * @since 0.7
         */
        public function upgrade($previous_version)
        {

        }

        /**
         * Check if there is a cron job for the "Before publishing" task.
         * If not, we schedule one.
         *
         * @todo: Avoid to run this on every request. Run on install/update, then show a notice to run it if needed
         */
        public function checkBeforePublishingCronJob()
        {
            if (!is_admin()) {
                return;
            }

            // Ignores auto-save
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // Ignores ajax calls
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return;
            }

            $newSchedule = $this->module->options->before_publishing_check_interval;

            if (wp_next_scheduled('publishpress_before_publishing_notifications')) {
                // Check if the interval is different of the scheduled job to re-schedule it
                $currentSchedule = wp_get_schedule('publishpress_before_publishing_notifications');

                if ($currentSchedule !== $newSchedule) {
                    wp_unschedule_hook('publishpress_before_publishing_notifications');
                }
            }

            // Check if we already have the job scheduled.
            if (!wp_next_scheduled('publishpress_before_publishing_notifications')) {
                wp_schedule_event(time(), $newSchedule,
                    'publishpress_before_publishing_notifications');
            }
        }

        /**
         * Print the content of the configure tab.
         */
        public function print_configure_view()
        {
            $this->configure_twig();

            echo $this->twig->render(
                'settings-tab.twig',
                [
                    'form_action'        => menu_page_url($this->module->settings_slug, false),
                    'options_group_name' => $this->module->options_group_name,
                    'module_name'        => $this->module->slug,
                ]
            );
        }

        /**
         * Register settings for notifications so we can partially use the Settings API
         * (We use the Settings API for form generation, but not saving)
         */
        public function register_settings()
        {
            /**
             *
             * General
             *
             */
            add_settings_section(
                $this->module->options_group_name . '_general',
                __('General:', 'publishpress-reminders'),
                '__return_false',
                $this->module->options_group_name
            );

            add_settings_field(
                'before_publishing_check_interval',
                __('Interval to look for scheduled posts:', 'publishpress-reminders'),
                [$this, 'settings_before_publishing_check_interval'],
                $this->module->options_group_name,
                $this->module->options_group_name . '_general'
            );
        }

        /**
         * Displays the field to customize the interval we should check for notifications related
         * to before publishing
         *
         * @param array
         */
        public function settings_before_publishing_check_interval($args = [])
        {
            $interval     = isset($this->module->options->before_publishing_check_interval) ? $this->module->options->before_publishing_check_interval : 'every_30_min';
            $schedules    = wp_get_schedules();
            $nextSchedule = wp_next_scheduled('publishpress_before_publishing_notifications');
            $diff         = $nextSchedule - time();

            $nextRun = false;
            if ($diff <= 0) {
                $nextRun = __('now', 'publishpress-permissions');
            } else {
                if ($diff < 60) {
                    $nextRun = sprintf(__('%s seconds', 'publishpress-reminders'), $diff);
                }
            }

            if (!$nextRun) {
                $nextRun = human_time_diff($nextSchedule);
            }

            echo '<select id="before_publishing_check_interval" name="publishpress_reminders_options[before_publishing_check_interval]">';

            foreach ($schedules as $schedule => $params) {
                $selected = ($interval == $schedule) ? 'selected="selected"' : '';
                echo "<option {$selected} value=\"{$schedule}\">{$params['display']}</option>";
            }

            echo '</select>';
            echo '&nbsp;<span>(' . __('Next run', 'publishpress-reminders') . ': ' . $nextRun . ')</span>';

            echo '<p class="description">' . __('How often should we check for scheduled posts? This enables PublishPress to process notifications for scheduled content.',
                    'publishpress-reminders')
                . '&nbsp;<a href="https://publishpress.com/docs/reminders/" target="_blank">' . __('Click here for more information.') . '</a></p>';
        }

        /**
         * Validate data entered by the user
         *
         * @param array $new_options New values that have been entered by the user
         *
         * @return array $new_options Form values after they've been sanitized
         */
        public function settings_validate($new_options)
        {
            $new_options = apply_filters('pp_reminders_validate_settings', $new_options);

            return $new_options;
        }

        /**
         * Enqueue scripts and stylesheets for the admin pages.
         *
         * @param string $hook_suffix
         */
        public function add_admin_scripts($hook_suffix)
        {
            if (in_array($hook_suffix, ['post.php', 'post-new.php'])) {
                if (PUBLISHPRESS_NOTIF_POST_TYPE_WORKFLOW === get_post_type()) {
                    wp_enqueue_script('pspprm-reminders-form', plugin_dir_url(__FILE__) . 'assets/js/reminders.js',
                        ['jquery', 'psppno-workflow-form', 'psppno-multiple-select'], PUBLISHPRESS_VERSION);
                }
            }
        }

        /**
         * @param array $classes
         *
         * @return array
         */
        public function addWorkflowStepEvent($classes)
        {
            $classes[] = '\\PublishPress\\Addon\\Reminders\\Workflow\\Step\\Event\\BeforePublishing';
            $classes[] = '\\PublishPress\\Addon\\Reminders\\Workflow\\Step\\Event\\AfterPublishing';

            return $classes;
        }

        /**
         * @param $schedules
         *
         * @return mixed
         */
        public function addCronSchedules($schedules)
        {
            $intervals = [
                10 => __('Every 10 minutes', 'publishpress-reminders'),
                15 => __('Every 15 minutes', 'publishpress-reminders'),
                20 => __('Every 20 minutes', 'publishpress-reminders'),
                30 => __('Every 30 minutes', 'publishpress-reminders'),
                45 => __('Every 45 minutes', 'publishpress-reminders'),
            ];

            foreach ($intervals as $minutes => $label) {
                $key = "every_{$minutes}_min";

                if (!array_key_exists($key, $schedules)) {
                    $schedules[$key] = [
                        'interval' => $minutes * MINUTE_IN_SECONDS,
                        'display'  => $label,
                    ];
                }
            }

            return $schedules;
        }

        public function shortcodePostMetaData($result, $item, $post, $attrs)
        {
            if (0 === strpos($item, 'meta')) {
                $arr = explode(':', $item);
                if (!empty($arr[1])) {
                    $meta = get_post_meta($post->ID, $arr[1], true);
                    if ($meta && is_scalar($meta)) {
                        if ('meta-date' == $arr[0]) {
                            $result = date_i18n(get_option('date_format'), $meta);
                        } else {
                            $result = $meta;
                        }
                    }
                }
            }

            return $result;
        }
    }
}
