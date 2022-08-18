<?php

namespace Wpo\Mail;

use \Wpo\Mail\Mailer;
use \Wpo\Services\Options_Service;
use Wpo\Services\Request_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Mail\Mail_Db')) {

    class Mail_Db
    {

        /**
         * Logs a wp_mail email message to the wpo365_mail table if that feature is enabled. 
         * Creates the table if it does not exist.
         * 
         * @since       17.0
         * 
         * @param       array       $wp_mail       WP Mail message as an array.
         * 
         * @return      mixed       Id of the row inserted or false if the row was not inserted.
         */
        public static function add_mail_log($wp_mail)
        {

            if (!Options_Service::get_global_boolean_var('mail_log')) {
                return $wp_mail;
            }

            $to = $wp_mail['to'];
            $subject = $wp_mail['subject'];
            $body = $wp_mail['message'];
            $headers = $wp_mail['headers'];
            $attachments = $wp_mail['attachments'];

            global $wpdb;

            if (!self::mail_table_exists()) {
                self::create_mail_table();
            }

            $table_name = self::get_mail_table_name();
            $data = array(
                'mail_to'           => \is_string($to) ? json_encode(array($to)) : json_encode($to),
                'mail_subject'      => $subject,
                'mail_body'         => $body,
                'mail_headers'      => \is_string($headers) ? json_encode(array($headers)) : json_encode($headers),
                'mail_attachments'  => json_encode($attachments),
                'mail_success'      => false,
                'mail_error'        => null,
            );

            $rows_inserted = $wpdb->insert(
                $table_name,
                $data
            );

            if ($rows_inserted !== 1) {
                Mailer::mailer_log('ERROR', __METHOD__ . ' -> Could not write mail log entry to the database (Check next line for the raw data that has not been inserted)');
                Mailer::mailer_log('DEBUG', $data);
            } else {
                // Memoize the ID of the row inserted so we can update it to report success or errors
                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
                $request->set_item('mail_log_id', $wpdb->insert_id);
            }

            return $wp_mail;
        }

        /**
         * Get the last inserted mail log entry for the specified recipient and updates it according. Returns false
         * 
         * @since   17.0
         * 
         * @param   bool    $success        The recipient string.
         * @param   string  $error_message  The recipient string.
         * 
         * @return  void
         */
        public static function update_mail_log($success = false, $error_message = null)
        {

            if (!Options_Service::get_global_boolean_var('mail_log') || !self::mail_table_exists()) {
                return false;
            }


            // Get the memoized ID of the current mail log entry
            $request_service = Request_Service::get_instance();
            $request = $request_service->get_request($GLOBALS['WPO_CONFIG']['request_id']);
            $mail_log_id = $request->get_item('mail_log_id');

            global $wpdb;

            if (!empty($mail_log_id)) {
                $table_name = self::get_mail_table_name();
                $results = $wpdb->get_results("SELECT * from $table_name WHERE id = $mail_log_id");
            }

            if (!empty($results) && sizeof($results) === 1) {

                if (empty($error_message)) {
                    $mail_error = $results[0]->mail_error;
                } elseif (empty($results[0]->mail_error)) {
                    $mail_error = $error_message;
                } else {
                    $mail_error = sprintf('%s | %s', $results[0]->mail_error, $error_message);
                }

                $update_result = $wpdb->update($table_name, array('mail_success' => $success, 'mail_error' => $mail_error), array('id' => intval($results[0]->id)));
            }
        }

        /**
         * Get a virtual page of a configurable number of rows from the mail log table.
         * 
         * @since   17.0
         * 
         * @param   int     $page       The zero-based page to start retrieving the next page.
         * @param   int     $page_size  The number of rows to retrieve.
         * @param   string  $filter     all or error
         * 
         * @return   array   Max. 100 rows from the mail log starting from the first row for the page.
         */
        public static function get_mail_log($start_row = 0, $page_size = 100, $filter = 'all')
        {

            if (!Options_Service::get_global_boolean_var('mail_log')) {
                return array();
            }

            global $wpdb;

            $table_name = self::get_mail_table_name();

            if (!self::mail_table_exists()) {
                Mailer::mailer_log('WARN', __METHOD__ . " -> Trying to get the mail log but database table $table_name not found");
                return array();
            }

            if ($start_row == 0) {
                $db_name = $wpdb->dbname;
                $next_id = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = '%s'",
                        $db_name,
                        $table_name
                    )
                );

                if (empty($next_id)) {
                    Mailer::mailer_log('DEBUG', __METHOD__ . " -> Cannot retrieve rows from the mail log table because the next ID is not initialized [$next_id]");
                    return array();
                }

                $start_row = \intval($next_id);
            }

            $filter_clause = $filter == 'error' ? " AND mail_success = false " : "";

            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id < %d " . $filter_clause . " ORDER BY id DESC LIMIT %d",
                $start_row,
                $page_size
            ));

            return $rows;
        }

        /**
         * Try to send the mail with the specified id again.
         * 
         * @since   17.0
         * 
         * @param   int     $id     The (wpo365_mail table's) id.
         * 
         * @return  bool    True if the mail was sent successfully.
         */
        public static function send_mail_again($id)
        {

            if (!\is_int($id)) {
                Mailer::mailer_log('WARN', __METHOD__ . " -> Trying to send mail again but the id $id provided is not valid");
                return false;
            }

            global $wpdb;

            $table_name = self::get_mail_table_name();

            if (!self::mail_table_exists()) {
                Mailer::mailer_log('WARN', __METHOD__ . " -> Trying to send mail again but database table $table_name not found");
                return false;
            }

            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            ));

            if (!\is_array($rows) || count($rows) != 1) {
                Mailer::mailer_log('WARN', __METHOD__ . " -> Trying to send mail again but could not find a matching database record for id $id");
                return false;
            }

            return wp_mail(
                json_decode($rows[0]->mail_to, true),
                $rows[0]->mail_subject,
                $rows[0]->mail_body,
                json_decode($rows[0]->mail_headers, true),
                json_decode($rows[0]->mail_attachments, true)
            );
        }

        /**
         * Helper method to the wpo365_mail table.
         * 
         * @since   17.0
         * 
         * @return  bool    True if truncated, false if the table was not found.
         */
        public static function truncate_mail_log()
        {

            global $wpdb;

            if (self::mail_table_exists()) {
                $table_name = self::get_mail_table_name();
                $wpdb->query("TRUNCATE TABLE $table_name");
                Mailer::mailer_log('DEBUG', __METHOD__ . " -> Truncated the wpo365_mail table successfully");
                return true;
            }

            Mailer::mailer_log('WARN', __METHOD__ . " -> Trying to truncate the mail log but the wpo365_mail table does not exist");

            return false;
        }

        /**
         * Helper method to create / update the custom Mail DB table used for logging.
         * 
         * @since   17.0
         * 
         * @return  void
         */
        private static function create_mail_table()
        {
            global $wpdb;

            $table_name = self::get_mail_table_name();

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY, 
                    mail_sent DATETIME DEFAULT NOW(),
                    mail_to TEXT NOT NULL,
                    mail_subject TEXT,
                    mail_body LONGTEXT,
                    mail_headers TEXT,
                    mail_attachments TEXT,
                    mail_success BOOLEAN,
                    mail_error TEXT
                    ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        /**
         * Helper method to centrally provide the custom WordPress table name.
         * 
         * @since 3.0
         * 
         * @return void
         */
        private static function get_mail_table_name()
        {

            global $wpdb;

            return $wpdb->prefix . "wpo365_mail";
        }

        /**
         * Helper method to check whether the custom WordPress table exists.
         * 
         * @since   3.0
         * 
         * @return boolean
         */
        private static function mail_table_exists()
        {

            global $wpdb;

            $table_name = self::get_mail_table_name();
            return $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        }
    }
}
