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

if (!class_exists('PP_Slack')) {
    /**
     * class PP_Slack
     */
    class PP_Slack extends PP_Module
    {

        const METADATA_TAXONOMY = 'pp_slack_meta';

        const METADATA_POSTMETA_KEY = "_pp_slack_meta";

        const SETTINGS_SLUG = 'pp-slack-settings';

        const THEME_FULL = 'full';

        const THEME_CLEAN = 'clean';

        public $module_name = 'slack';

        protected $requirement_instances;

        /**
         * Flag to assist conditional loading
         */
        private $twig_configured = false;

        public $module;

        /**
         * Construct the PP_Slack class
         */
        public function __construct()
        {
            $this->twigPath = dirname(dirname(dirname(__FILE__))) . '/twig';

            $this->module_url = $this->get_module_url(__FILE__);

            // Register the module with PublishPress
            $args = [
                'title'                => __('Slack', 'publishpress-slack'),
                'short_description'    => false,
                'extended_description' => false,
                'module_url'           => $this->module_url,
                'icon_class'           => 'dashicons dashicons-feedback',
                'slug'                 => 'slack',
                'default_options'      => [
                    'enabled'                  => 'on',
                    'post_types'               => ['post'],
                    'show_warning_icon_submit' => 'no',
                    'license_key'              => '',
                    'license_status'           => '',
                    'channel'                  => 'email',
                    'notification_theme'       => self::THEME_FULL,
                ],
                'configure_page_cb'    => 'print_configure_view',
                'options_page'         => true,
            ];

            // Apply a filter to the default options
            $args['default_options'] = apply_filters('pp_slack_requirements_default_options', $args['default_options']);

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

            $function = new Twig_SimpleFunction('__', function ($id) {
                return __($id, 'publishpress-slack');
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

            add_filter('publishpress_notif_workflow_steps_channel', [$this, 'filter_workflow_steps_channel']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

            /**
             * Filters the option to enable or not notifications. Allow to block
             * the notifications to customize the workflow or process.
             *
             * @param bool $enable_notifications
             */
            $enable_notifications = apply_filters('publishpress_slack_enable_notifications', true);

            if ($enable_notifications) {
                // Cancel the PublishPress notifications
                remove_all_actions('pp_send_notification_status_update');
                remove_all_actions('pp_send_notification_comment');

                // Registers the Slack notifications
                add_action('pp_send_notification_status_update', [$this, 'send_notification'], 9);
                add_action('pp_send_notification_comment', [$this, 'send_notification'], 9);
            }

            add_filter('publishpress_slack_text', [$this, 'filter_slack_text']);
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
         * Filters the list of classes to be loaded at the Channel step for
         * the notification workflows
         *
         * @param array $classes
         */
        public function filter_workflow_steps_channel($classes)
        {
            $classes[] = '\\PublishPress\\Addon\\Slack\\Workflow\\Step\\Channel\\Slack';

            return $classes;
        }

        /**
         * Generate a link to one of the editorial metadata actions
         *
         * @param array $args (optional) Action and any query args to add to the URL
         *
         * @return string $link Direct link to complete the action
         * @since 0.7
         *
         */
        protected function get_link($args = [])
        {
            $args['page']   = 'pp-modules-settings';
            $args['module'] = 'pp-slack-settings';

            return add_query_arg($args, get_admin_url(null, 'admin.php'));
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

        public function enqueue_admin_scripts()
        {
            if (isset($_GET['page']) && $_GET['page'] === 'pp-notif-log') {
                wp_enqueue_style(
                    'publishpress-slack-notifications',
                    PUBLISHPRESS_PRO_PLUGIN_URL . 'modules/slack/assets/css/notification-workflow.css',
                    [],
                    PUBLISHPRESS_VERSION,
                    'screen'
                );
            }
        }

        /**
         * Register settings for notifications so we can partially use the Settings API
         * (We use the Settings API for form generation, but not saving)
         */
        public function register_settings()
        {
            /**
             *
             * Post types
             */
            $section_id = $this->module->options_group_name . '_slack';

            add_settings_section(
                $section_id,
                __('General:', 'publishpress-slack'),
                '__return_false',
                $this->module->options_group_name
            );


            add_settings_field(
                'service_url',
                __('Webhook URL:', 'publishpress-slack'),
                [$this, 'settings_service_url'],
                $this->module->options_group_name,
                $section_id
            );

            add_settings_field(
                'username',
                __('Username:', 'publishpress-slack'),
                [$this, 'settings_username'],
                $this->module->options_group_name,
                $section_id
            );

            add_settings_field(
                'channel',
                __('Default channel:', 'publishpress-slack'),
                [$this, 'settings_channel'],
                $this->module->options_group_name,
                $section_id
            );

            add_settings_field(
                'notification_theme',
                __('Notification theme:', 'publishpress-slack'),
                [$this, 'settings_notification_theme'],
                $this->module->options_group_name,
                $section_id
            );
        }

        /**
         * Displays the field to select the service URL.
         *
         * @param array
         */
        public function settings_service_url($args = [])
        {
            $id    = $this->module->options_group_name . '_service_url';
            $value = isset($this->module->options->service_url) ? $this->module->options->service_url : '';

            echo '<label for="' . $id . '">';
            echo '<input type="text" style="width: 100%" value="' . $value . '" id="' . $id . '" name="' . $this->module->options_group_name . '[service_url]" />';
            echo '<br>' . __('Your Slack incoming webhooks URL.', 'publishpress-slack');
            echo '&nbsp;<a href="https://my.slack.com/services/new/incoming-webhook/" target="_blank">' . __('Click here to generate the URL',
                    'publishpress-slack') . '</a>';
            echo '</label>';
        }

        /**
         * Displays the field to select the notification theme.
         *
         * @param array
         */
        public function settings_notification_theme($args = [])
        {
            $id             = $this->module->options_group_name . '_notification_theme';
            $selectedOption = isset($this->module->options->notification_theme) ? $this->module->options->notification_theme : self::THEME_FULL;

            echo '<label for="' . $id . '">';

            $options = [
                self::THEME_FULL  => [
                    'label'   => __('Full - Notification body with action buttons', 'publishpress-slack'),
                    'preview' => PUBLISHPRESS_PRO_PLUGIN_URL . 'modules/slack/assets/img/theme-preview-full.png?version=' . PUBLISHPRESS_PRO_VERSION,
                ],
                self::THEME_CLEAN => [
                    'label'   => __('Clean - Only the notification body', 'publishpress-slack'),
                    'preview' => PUBLISHPRESS_PRO_PLUGIN_URL . 'modules/slack/assets/img/theme-preview-clean.png?version=' . PUBLISHPRESS_PRO_VERSION,
                ],
            ];

            foreach ($options as $value => $option) {
                echo '<input type="radio" ' . checked($selectedOption,
                        $value,
                        false) . ' id="' . $id . '_' . $value . '" value="' . $value . '" name="' . $this->module->options_group_name . '[notification_theme]" />';
                echo '<label for="' . $id . '_' . $value . '">' . $option['label'] . ' <br><img src="' . $option['preview'] . '" style="width: 400px; margin-top: 10px; margin-left: 30px;"></label><br/><br/>';
            }

            echo '</label>';
        }

        /**
         * Displays the field to select the channel
         * close to the submit button
         *
         * @param array
         */
        public function settings_channel($args = [])
        {
            $id    = $this->module->options_group_name . '_channel';
            $value = isset($this->module->options->channel) ? $this->module->options->channel : '';

            echo '<label for="' . $id . '">';
            echo '<input type="text" style="min-width: 200px" value="' . $value . '" id="' . $id . '" name="' . $this->module->options_group_name . '[channel]" />';
            echo '<br>' . __('Default channel in which notifications will be sent to. For example #general, or @username. Users can override this on their profile.',
                    'publishpress-slack');
            echo '</label>';
        }

        /**
         * Displays the field to select the username
         * close to the submit button
         *
         * @param array
         */
        public function settings_username($args = [])
        {
            $id    = $this->module->options_group_name . '_username';
            $value = isset($this->module->options->username) ? $this->module->options->username : '';

            echo '<label for="' . $id . '">';
            echo '<input type="text" style="min-width: 200px" value="' . $value . '" id="' . $id . '" name="' . $this->module->options_group_name . '[username]" />';
            echo '<br>' . __('Username displayed in the notification . For example PublishPress Slack',
                    'publishpress-slack');
            echo '</label>';
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
            $new_options = apply_filters('pp_slack_validate_settings', $new_options);

            return $new_options;
        }

        /**
         * Gets a simple phrase containing the formatted date and time that the post is scheduled for.
         *
         * @param obj $post Post object
         *
         * @return str    $scheduled_datetime The scheduled datetime in human-readable format
         * @since 0.8
         *
         */
        private function get_scheduled_datetime($post)
        {
            $scheduled_ts = strtotime($post->post_date);

            $date = date_i18n(get_option('date_format'), $scheduled_ts);
            $time = date_i18n(get_option('time_format'), $scheduled_ts);

            return sprintf(__('%1$s at %2$s', 'publishpress'), $date, $time);
        }

        private function get_theme()
        {
            $theme = isset($this->module->options->notification_theme) && !empty($this->module->options->notification_theme)
                ? $this->module->options->notification_theme : self::THEME_FULL;

            // Make sure we have a valid theme set.
            if (!in_array($theme, [self::THEME_CLEAN, self::THEME_FULL])) {
                $theme = self::THEME_FULL;
            }

            return $theme;
        }

        private function get_post_edit_link($post_id)
        {
            $admin_path = 'post.php?post=' . $post_id . '&action=edit';

            return htmlspecialchars_decode(admin_url($admin_path));
        }

        private function get_post_view_link($post_id, $status)
        {
            if ($status != 'publish') {
                $view_link = add_query_arg(['preview' => 'true'], wp_get_shortlink($post_id));
            } else {
                $view_link = htmlspecialchars_decode(get_permalink($post_id));
            }

            return $view_link;
        }

        public function send_notification($args)
        {
            $post_id = $args['event_args']['params']['post_id'];

            // Get current user
            $current_user = wp_get_current_user();

            $edit_link = $this->get_post_edit_link($post_id);
            $view_link = $this->get_post_view_link($post_id, $args['event_args']['params']['new_status']);

            $theme = $this->get_theme();

            $actions = [];
            $body = $this->sanitizeHTML($args['body']);

            switch ($theme) {
                case self::THEME_FULL:
                    $comment_link = $edit_link . '#editorialcomments/add';

                    $gravatar_hash = md5(strtolower($current_user->user_email));
                    $gravatar_url  = 'https://www.gravatar.com/avatar/' . $gravatar_hash;

                    $actions = [
                        'type' => 'actions',
                        'elements' => [
                            [
                                'type' => 'button',
                                'text' => [
                                    'type' => 'plain_text',
                                    'text' => __('Comment', 'publishpress-slack'),
                                    'emoji' => true,
                                ],
                                'value' => 'comment',
                                'url'  => $comment_link,
                                'action_id' => 'add_comment',
                            ],
                            [
                                'type' => 'button',
                                'text' => [
                                    'type' => 'plain_text',
                                    'text' => __('Edit', 'publishpress-slack'),
                                    'emoji' => true,
                                ],
                                'value' => 'edit',
                                'url'  => $edit_link,
                                'action_id' => 'edit_post',
                            ],
                            [
                                'type' => 'button',
                                'text' => [
                                    'type' => 'plain_text',
                                    'text' => __('View', 'publishpress-slack'),
                                    'emoji' => true,
                                ],
                                'value' => 'view',
                                'url'  => $view_link,
                                'action_id' => 'view_post',
                            ],
                        ],
                    ];
                    break;
            }

            /**
             * @param array $actions
             * @param array $args Indexes: channel, content, action, post_id, post_title, post_type, current_user.
             *
             * @return array
             */
            $actions = apply_filters('publishpress_slack_actions', $actions, $args);

            $channel = '';
            if (isset($args['channel'])) {
                $channel = $args['channel'];
            }

            /**
             * @param string $channel
             * @param array $args Indexes: channel, content, action, post_id, post_title, post_type, current_user.
             *
             * @return string
             */
            $channel = apply_filters('publishpress_slack_channel', $channel, $args);

            /**
             * @param string $text
             * @param array $args Indexes: channel, content, action, post_id, post_title, post_type, current_user.
             *
             * @return string
             */
            $text = apply_filters('publishpress_slack_text', $body, $args);

            return $this->send_slack_message($text, $actions, $channel);
        }

        private function sanitizeHTML($text)
        {
            $text = preg_replace('|<a[^>]*href\s*=\s*["\']([^"\']+)["\'][^>]*>([^<]+)</a>|i', '::::$1|$2;;;;', $text);
            $text = wp_strip_all_tags($text);
            $text = html_entity_decode($text);
            $text = str_replace(['::::', ';;;;'], ['<', '>'], $text);

            return $text;
        }

        public function filter_slack_text($text)
        {
            $text = preg_replace('/(@[A-Z0-9]+)/', '<$1>', $text);

            return $text;
        }

        public function send_slack_message($text, $actions = null, $channel = '')
        {
            $service_url = isset($this->module->options->service_url) ? $this->module->options->service_url : '';
            $return      = [
                'response' => false,
                'payload'  => [],
            ];

            if (empty($channel)) {
                $channel = isset($this->module->options->channel) ? $this->module->options->channel : '';
            }

            if (!empty($service_url) && !empty($channel)) {
                $username = isset($this->module->options->username) ? $this->module->options->username : 'Publishpress Slack';

                /**
                 * @param string $username
                 *
                 * @return string
                 */
                $username = apply_filters('publishpress_slack_username', $username, $channel);

                /**
                 * @param string $icon_url
                 *
                 * @return string
                 */
                $icon_url = apply_filters('publishpress_slack_icon_url',
                    'https://publishpress.com/wp-content/uploads/2017/03/publishpress-slack.jpg', $username, $channel);

                $payload = [
                    'channel'  => $channel,
                    'username' => $username,
                    'icon_url' => $icon_url,
                    'blocks' => [
                        [
                            'type' => 'section',
                            'text' => [
                                'type' => 'mrkdwn',
                                'text' => $text,
                            ]
                        ]
                    ]
                ];

                if (!empty($actions)) {
                    $payload['blocks'][] = $actions;
                }

                $args = [
                    'user-agent' => sprintf('%s/%s', PP_SLACK_NAME, PUBLISHPRESS_PRO_VERSION),
                    'body'       => json_encode($payload),
                    'headers'    => ['Content-Type' => 'application/json'],
                ];

                if ((int)ini_get('max_execution_time') < 300) {
                    @set_time_limit(300);
                }

                $response = wp_remote_post($service_url, $args);

                if (is_wp_error($response)) {
                    $return['response'] = $response;
                } else {
                    $responseStatus = intval(wp_remote_retrieve_response_code($response));

                    if (200 !== $responseStatus) {
                        $message            = wp_remote_retrieve_body($response);
                        $return['response'] = new WP_Error('slack_unexpected_response', $message);
                    } else {
                        $return['response'] = true;
                    }
                }

                $return['payload'] = $payload;
            }

            return $return;
        }
    }
}
