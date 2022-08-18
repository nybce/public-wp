<?php

    namespace Wpo\Sync;

    use \Wpo\Services\Log_Service;
        
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    if( !class_exists( '\Wpo\Sync\Sync_Db' ) ) {

        class Sync_Db {

            /**
             * Helper method to create / update the custom WordPress table.
             * 
             * @since 3.0
             * 
             * @return void
             */
            public static function create_user_sync_table() {

                global $wpdb;

                $table_name = self::get_user_sync_table_name();

                $charset_collate = $wpdb->get_charset_collate();

                $sql = "CREATE TABLE $table_name (
                    wp_id int(11) DEFAULT 0 NOT NULL,
                    upn varchar(100) NOT NULL,
                    first_name varchar(100) DEFAULT '' NOT NULL,
                    last_name varchar(100) DEFAULT '' NOT NULL,
                    full_name varchar(100) DEFAULT '' NOT NULL,
                    email varchar(100) DEFAULT '' NOT NULL,
                    sync_job_id varchar(50) NOT NULL,
                    name varchar(100) DEFAULT '' NOT NULL,
                    sync_job_status varchar(11) NOT NULL,
                    record_type varchar(20) NOT NULL,
                    notes varchar(500) DEFAULT '',
                    KEY record_type (record_type),
                    KEY sync_job_id (sync_job_id),
                    KEY wp_id (wp_id),
                    KEY sync_job_status (sync_job_status),
                    PRIMARY KEY  (upn, sync_job_id)
                    ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }

            /**
             * Helper method to centrally provide the custom WordPress table name.
             * 
             * @since 3.0
             * 
             * @return void
             */
            public static function get_user_sync_table_name() {

                global $wpdb;

                return $wpdb->prefix . "wpo365_user_sync";

            }

            /**
             * Helper method to check whether the custom WordPress table exists.
             * 
             * @since   3.0
             * 
             * @return boolean
             */
            public static function user_sync_table_exists( $force = false ) {

                global $wpdb;

                $table_name = self::get_user_sync_table_name();
                $exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name;

                if ( ! $exists && ! $force ) {
                    return false;
                }

                /**
                 * @since   15.0    Updated to add a notes column and change PRIMARY KEY.
                 */

                if ( $exists ) {
                    $has_notes = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'notes'"  );
                    $has_pk = $wpdb->get_results( "SHOW INDEX FROM $table_name WHERE Key_name = 'PRIMARY'" );
    
                    if ( empty( $has_notes ) || empty( $has_pk ) || sizeof( $has_pk ) != 2 ) {
                        $sql = "DROP TABLE IF EXISTS $table_name";
                        $wpdb->query( $sql );
                        $exists = false;
                        Log_Service::write_log( 'WARN', __METHOD__ . ' -> Deleted existing table for WPO365 user synchronization [' . $table_name . ']' );
                    }
                }                

                if ( ! $exists ) {
                    self::create_user_sync_table();
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Created new table for WPO365 user synchronization [' . $table_name . ']' );
                }
                
                return true;
            }
        }
    }
