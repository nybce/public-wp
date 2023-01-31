<?php

namespace Wpo\Mail;

use \Wpo\Mail\Mail_Authorization_Helpers;
use \Wpo\Mail\Mailer;
use \Wpo\Services\Graph_Service;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;
use WP_Error;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Mail\Mail_Attachments')) {

    class Mail_Attachments
    {
        /**
         * 
         * @param   array   $large_attachments 
         * @param   array   $attachment 
         * 
         * @return void 
         */
        public static function add_large_attachment(&$large_attachments, $attachment)
        {
            $large_attachments[] = $attachment;
        }

        /**
         * Uploads large attachments to a previously created draft email message and sends it 
         * once all attachments are uploaded.
         * 
         * @since   20.0
         * 
         * @param   string  $message_id         ID of the draft email message.
         * @param   string  $mail_user          Mail account e.g. user@domain.
         * @param   array   $large_attachments  Array of large attachments to attach to the draft email before sending.
         */
        public static function send_draft_email($message_id, $mail_user, $large_attachments)
        {
            foreach ($large_attachments as $attachment) {
                $upload_result = self::upload_attachment($message_id, $mail_user, $attachment);

                if (is_wp_error($upload_result)) {
                    Log_Service::write_log('ERROR', $upload_result->get_error_message());
                }
            }

            $scope = Options_Service::get_global_boolean_var('mail_send_shared') ? 'Mail.Send.Shared' : 'Mail.Send';
            $access_token = Mail_Authorization_Helpers::get_mail_access_token($scope);
            $query = "/users/$mail_user/messages/$message_id/send";

            if (is_wp_error($access_token)) {
                $message_sent_result = $access_token;
            } else {
                $message_sent_result = Mailer::mg_fetch($query, '', $access_token['access_token']);
            }

            if (Graph_Service::is_fetch_result_ok($message_sent_result, "Could not sent draft email with ID $message_id from account $mail_user using Microsoft Graph")) {
                Log_Service::write_log('DEBUG', sprintf(
                    '%s -> WordPress email sent successfully using Microsoft Graph',
                    __METHOD__
                ));

                if (class_exists('\Wpo\Mail\Mail_Db')) {
                    \Wpo\Mail\Mail_Db::update_mail_log(
                        true
                    );
                }

                return true;
            }

            if (class_exists('\Wpo\Mail\Mail_Db')) {

                if (is_wp_error($message_sent_result)) {
                    $log_message = $message_sent_result->get_error_message();
                } else {
                    $log_message = "Could not sent draft email with ID $message_id from account $mail_user using Microsoft Graph [see log for errors]";
                }

                \Wpo\Mail\Mail_Db::update_mail_log(
                    false,
                    $log_message
                );
            }

            return false;
        }

        /**
         * Creates an upload session to add large attachments to a draft email.
         * 
         * @param   string   $message_id 
         * @param   string   $mail_user 
         * @param   array   $attachment 
         * 
         * @return WP_Error|bool 
         */
        private static function upload_attachment($message_id, $mail_user, $attachment)
        {
            if (!file_exists($attachment[0])) {
                $error_message = sprintf('%s -> Could not find attachment with path %s');
                return new WP_Error('FileNotFoundException', $error_message);
            }

            $scope = Options_Service::get_global_boolean_var('mail_send_shared') ? 'Mail.ReadWrite.Shared' : 'Mail.ReadWrite';
            $access_token = Mail_Authorization_Helpers::get_mail_access_token($scope);
            $query = "/users/$mail_user/messages/$message_id/attachments/createUploadSession";
            $body = self::attachment_item_encode($attachment);

            if (is_wp_error($access_token)) {
                return $access_token;
            }

            $log_message = function ($starts_with) use ($attachment, $message_id, $mail_user) {
                return sprintf(
                    "%s -> %s mail attachment with path %s for message with ID %s for account %s",
                    __METHOD__,
                    $starts_with,
                    $attachment[0],
                    $message_id,
                    $mail_user
                );
            };

            $upload_session_result = Mailer::mg_fetch($query, $body, $access_token['access_token']);

            if (!Graph_Service::is_fetch_result_ok($upload_session_result, $log_message('Failed to create upload session for'), 'WARN') || !isset($upload_session_result['payload']) || !isset($upload_session_result['payload']['uploadUrl'])) {
                return new WP_Error('GraphFetchException', $log_message('Failed to create upload session'));
            }

            Log_Service::write_log('DEBUG', $log_message('Successfully created upload session for'));

            $upload_url = $upload_session_result['payload']['uploadUrl'];
            $file_size = filesize($attachment[0]);
            $chunk_size = 1048576; // Chunks of 1 Mb
            $upload_start = 0;
            $handle = fopen($attachment[0], 'rb');

            while ($upload_start < $file_size) {
                $upload_last = $file_size < $upload_start + $chunk_size ? $file_size - 1 : $upload_start + $chunk_size - 1;
                $contents = fread($handle, $chunk_size);
                $content_length = $upload_start + $chunk_size < $file_size ? $chunk_size : $file_size - $upload_start;
                $content_range = sprintf(
                    'bytes %s-%s/%s',
                    $upload_start,
                    $upload_last,
                    $file_size
                );

                $headers = array('Content-Type' => 'application/octet-stream');
                $headers['Content-Length'] = $content_length;
                $headers['Content-Range'] = $content_range;

                $upload_result = Mailer::mg_fetch($upload_url, $contents, null, 'PUT', $headers);

                if (!Graph_Service::is_fetch_result_ok($upload_result, $log_message('Failed to upload'), 'WARN')) {
                    return new WP_Error('GraphFetchException', $log_message('Failed to upload'));
                }

                $upload_start += strlen($contents);
                fseek($handle, $upload_start);
            }

            fclose($handle);

            Log_Service::write_log('DEBUG', $log_message('Successfully uploaded'));

            return true;
        }

        /**
         * 
         * @param mixed $attachment 
         * @return string|false 
         */
        private static function attachment_item_encode($attachment)
        {
            $attachment_item = array(
                "AttachmentItem" => array(
                    "attachmentType" => "file",
                    "name" => $attachment[2],
                    "size" => filesize($attachment[0]),
                ),
            );

            return json_encode($attachment_item);
        }
    }
}
