<?php

namespace Wpo\Mail;

use \Wpo\Core\Permissions_Helpers;
use \Wpo\Mail\Mail_Db;
use \Wpo\Mail\Mailer;
use \Wpo\Services\Ajax_Service;
use \Wpo\Services\Log_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Mail\Mail_Ajax_Service')) {

    class Mail_Ajax_Service
    {

        /**
         * Gets the debug log
         *
         * @since 7.11
         *
         * @return void
         */
        public static function get_mail_log()
        {
            // Verify AJAX request
            $current_user = Ajax_Service::verify_ajax_request('to get a portion of the wpo365 mail log');

            if (false === Permissions_Helpers::user_is_admin($current_user)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> User has no permission to get a portion of the wpo365 mail log from AJAX service');
                wp_die();
            }

            Ajax_Service::verify_POSTed_data(array('start_row', 'page_size', 'filter')); // -> wp_die()

            $start_row = ctype_digit($_POST['start_row']) ? \intval($_POST['start_row']) : 0;
            $page_size = ctype_digit($_POST['page_size']) ? \intval($_POST['page_size']) : 0;
            $filter = $_POST['filter'] == 'error' ? 'error' : 'all';
            $log = Mail_Db::get_mail_log($start_row, $page_size, $filter);
            Ajax_Service::AJAX_response('OK', '', '', json_encode($log));
        }

        /**
         * Try to send the mail with id equal to $_POST[ 'id' ] again.
         * 
         * @since   17.0
         * 
         * @return  void
         */
        public static function send_mail_again()
        {
            // Verify AJAX request
            $current_user = Ajax_Service::verify_ajax_request('to send mail again');

            if (false === Permissions_Helpers::user_is_admin($current_user)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> User has no permission to send mail again from AJAX service');
                wp_die();
            }

            Ajax_Service::verify_POSTed_data(array('id')); // -> wp_die()

            if (!ctype_digit($_POST['id'])) {
                $message = sprintf('Cannot convert parameter id to integer when trying to send mail again [%s]', $_POST['id']);
                Log_Service::write_log('ERROR', __METHOD__ . " -> $message");
                Ajax_Service::AJAX_response('NOK', '', $message, null);
                wp_die();
            }

            $id =  \intval($_POST['id']);
            $success = Mail_Db::send_mail_again($id);
            Ajax_Service::AJAX_response('OK', '', '', json_encode(array('mail_success' => $success)));
        }

        /**
         * Truncates the wpo365_mail table.
         * 
         * @since   17.0
         * 
         * @return  void
         */
        public static function truncate_mail_log()
        {
            // Verify AJAX request
            $current_user = Ajax_Service::verify_ajax_request('to truncate the wpo365_mail table');

            if (false === Permissions_Helpers::user_is_admin($current_user)) {
                Log_Service::write_log('ERROR', __METHOD__ . ' -> User has no permission to truncate the wpo365_mail table from AJAX service');
                wp_die();
            }

            $success = Mail_Db::truncate_mail_log();
            Ajax_Service::AJAX_response('OK', '', '', json_encode(array('mail_success' => $success)));
        }
    }
}
