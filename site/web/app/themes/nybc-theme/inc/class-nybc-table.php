<?php
/**
 * NYBC  Articulate Course post type class
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Table' ) ) {
	/**
	 * Table class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Table {

		/**
		 * CSV delimiter
		 *
		 * @var string
		 */
		private static $delimiter = ';';

		/**
		 *  NYBC_Table Constructor
		 */
		public function __construct() {

			if ( ! class_exists( 'TablePress' ) ) {
				return;
			}

			add_action( 'init', array( 'NYBC_Table', 'export' ) );
			add_filter( 'tablepress_table_output', array( 'NYBC_Table', 'tablepress_table_output' ), 10, 3 );

		}

		/**
		 *  Export table in CSV
		 */
		public static function export() {

			if (
				isset( $_POST['table_id'] )
				&& isset( $_POST['action'] )
				&& 'table_export' === $_POST['action']
				&& isset( $_POST['nonce'] )
				&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'table_export' )
			) {

				$table_id = sanitize_text_field( wp_unslash( $_POST['table_id'] ) );

				$exporter = TablePress::load_class( 'TablePress_Export', 'class-export.php', 'classes' );
				$table    = TablePress::$model_table->load( $table_id, true, true );
				if ( is_wp_error( $table ) ) {
					die( 'Error' );
				}
				if ( isset( $table['is_corrupted'] ) && $table['is_corrupted'] ) {
					die( 'Error. Table corrupted' );
				}
				$download_filename = sprintf( '%1$s-%2$s-%3$s.%4$s', $table['id'], $table['name'], wp_date( 'Y-m-d' ), 'csv' );
				$download_filename = sanitize_file_name( $download_filename );
				$download_data     = $exporter->export_table( $table, 'csv', self::$delimiter );

				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: application/octet-stream' );
				header( "Content-Disposition: attachment; filename=\"{$download_filename}\"" );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . strlen( $download_data ) );
				@ob_end_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				flush();
				echo esc_html( $download_data );
				exit;
			}

		}

		/**
		 *  Add Articulate courses admin table columns
		 *
		 * @param string $output table html.
		 * @param array  $table table data.
		 * @param array  $render_options table options.
		 *
		 * @return string
		 */
		public static function tablepress_table_output( $output, $table, $render_options ) {
			ob_start();
			?>
			<form action="" method="post" class="text-center">
				<input type="hidden" name="table_id" value="<?php echo esc_attr( $table['id'] ); ?>">
				<input type="hidden" name="action" value="table_export">
				<?php wp_nonce_field( 'table_export', 'nonce' ); ?>
				<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Load CSV', 'nybc' ); ?></button>
			</form>
			<?php
			$output .= ob_get_clean();

			return $output;
		}

	}

	new NYBC_Table();
}
