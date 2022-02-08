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

require_once '/site/vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

if ( ! class_exists( 'NYBC_Articulate' ) ) {
	/**
	 * Articulate Course post type class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Articulate {

		/**
		 * Main course file
		 *
		 * @var array
		 */
		private static $main_file = ['story.html', 'presentation.html'];

		/**
		 * Max files
		 *
		 * @var int
		 */
		private static $max_files = 30;

		/**
		 * Azure Storage Account name
		 *
		 * @var string
		 */
		private static $azure_storage_account_name = '';
		/**
		 * Azure Storage Account key
		 *
		 * @var string
		 */
		private static $azure_storage_account_key = '';
		/**
		 * Azure Storage_container name
		 *
		 * @var string
		 */
		private static $azure_storage_container = '';

		/**
		 *  NYBC_Articulate Constructor
		 */
		public function __construct() {
			if ( function_exists( 'get_field' ) ) {
				self::$azure_storage_account_name = get_field( 'azure_storage_account_name', 'options' );
				self::$azure_storage_account_key  = get_field( 'azure_storage_account_key', 'options' );
				self::$azure_storage_container    = get_field( 'azure_storage_storage_container', 'options' );
				$max_files                        = get_field( 'number_of_files_per_one_push', 'options' );

				if ( ! empty( $max_files ) ) {
					self::$max_files = $max_files;
				}
			}

			if ( empty( self::$azure_storage_account_name ) || empty( self::$azure_storage_account_key ) || empty( self::$azure_storage_container ) ) {
				return;
			}
			add_action( 'init', array( 'NYBC_Articulate', 'taxonomy_post_type' ) );
			add_action( 'admin_init', array( 'NYBC_Articulate', 'add_load_metabox' ) );
			add_action( 'wp_ajax_import_articulate_course', array( 'NYBC_Articulate', 'import_articulate_course' ) );

			add_filter( 'manage_articulate_posts_columns', array( 'NYBC_Articulate', 'manage_articulate_posts_columns' ) );
			add_action( 'manage_articulate_posts_custom_column', array( 'NYBC_Articulate', 'manage_articulate_posts_custom_column' ), 10, 2 );

			/**
			 *  Add post type fields
			 */
			get_template_part( 'inc/acf/articulate' );
		}

		/**
		 *  Add Articulate courses admin table columns
		 *
		 * @param array $columns table columns.
		 *
		 * @return array
		 */
		public static function manage_articulate_posts_columns( $columns ) {
			$columns_1 = array_slice( $columns, 0, count( $columns ) - 1, true );
			$columns_2 = array_slice( $columns, -1, null, true );
			$columns   = array_merge( $columns_1, array( 'azure_link' => esc_html__( 'Azure Storage Link', 'nybc' ) ), $columns_2 );

			return $columns;
		}

		/**
		 *  Add Articulate courses admin table columns
		 *
		 * @param string $column table column name.
		 * @param int    $post_id post ID.
		 */
		public static function manage_articulate_posts_custom_column( $column, $post_id ) {
			switch ( $column ) {
				case 'azure_link':
					$course_link = get_field( 'course_link', $post_id );
					echo esc_html( $course_link ? $course_link : '-' );
					break;
			}
		}

		/**
		 *  Register taxonomy and post types
		 */
		public static function taxonomy_post_type() {

			register_post_type(
				'articulate',
				array(
					'labels'            => array(
						'name'               => esc_html__( 'Articulate Course', 'nybc' ),
						'singular_name'      => esc_html__( 'Articulate Course', 'nybc' ),
						'add_new'            => esc_html__( 'Add Articulate Course', 'nybc' ),
						'add_new_item'       => esc_html__( 'Add Articulate Course', 'nybc' ),
						'edit_item'          => esc_html__( 'Edit Articulate Course', 'nybc' ),
						'new_item'           => esc_html__( 'New Articulate Course', 'nybc' ),
						'view_item'          => esc_html__( 'View Articulate Course', 'nybc' ),
						'search_items'       => esc_html__( 'Search Articulate Course', 'nybc' ),
						'not_found'          => esc_html__( 'Articulate Course not found', 'nybc' ),
						'not_found_in_trash' => esc_html__( 'Articulate Course not found in trash', 'nybc' ),
						'parent_item_colon'  => esc_html__( 'Articulate Course', 'nybc' ),
						'menu_name'          => esc_html__( 'Articulate Courses', 'nybc' ),
					),
					'show_in_nav_menus' => true,
					'show_ui'           => true,
					'public'            => false,
					'show_in_rest'      => false,
					'menu_position'     => 20,
					'supports'          => array( 'title' ),
					'menu_icon'         => null,
					'has_archive'       => false,
				)
			);

		}

		/**
		 *  Meta box init for upload Articulate course
		 */
		public static function add_load_metabox() {
			add_meta_box( 'articulate_load_meta_box', 'Load Articulate Course', array( 'NYBC_Articulate', 'articulate_load_meta_box' ), 'articulate', 'normal', 'high' );
		}

		/**
		 *  Meta box create for upload Articulate course
		 */
		public static function articulate_load_meta_box() {
			?>
			<form  id="import-form" style="position: relative">
				<?php wp_nonce_field( 'articulate_load', 'nonce' ); ?>
				<table width="100%">
					<tr>
						<td style="width: 25%">Choose File</td>
						<td>
							<input type="file" style="" name="course_file" accept=".zip" value="" />
						</td>
						<td style="width: 25%">
							<div class="import-loader" style="display: none; color: red">
								Please wait...
								<img src="<?php echo esc_url( NYBC_IMG_URI ); ?>/ajax-loading.gif"  style="position:absolute;top:-10px;width:50px;"/>
							</div>
						</td>
					</tr>
				</table>
			</form>
			<script>
				jQuery( document ).ready( function( $ ) {
					function parseJson(str) {
						var j;
						try {
							j = JSON.parse(str);
						} catch (e) {
							return false;
						}
						return j;
					}

					function nextFilesLoad(dir){
						var input = $('input[name="course_file"]');
						var form = input.closest('form');
						var formData = new FormData(), file = input.get(0).files[0];

						if(file) formData.append('course_file',  file);

						formData.append('action', 'import_articulate_course');
						formData.append('nonce',  form.find('input[name="nonce"]').val());
						formData.append('post_id',  $('#post_ID').val());

						if(dir) formData.append('dir',  dir);

						form.find('.import-loader').css('display','inline-block');
						input.val('');
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: formData,
							contentType: false,
							processData: false,
							timeout: 0,
							success: function(response){
								var data = parseJson(response);
								if(data) {
									if(!data.more) {
										form.find('.import-loader').hide();
									}else{
										nextFilesLoad(data.dir)
									}
									if(data.url) $('[data-name="course_link"] input').val(data.url);
								} else {
									$('[data-name="course_link"] input').val(response);
								}
							},
							error: function(xhr, status) {
								$('[data-name="course_link"] input').val(status);
								form.find('.import-loader').hide();
							}

						});
					}

					$('input[name="course_file"]').on('change',function(){
						nextFilesLoad();
						return false;
					})
				});
			</script>
			<?php
		}

		/**
		 *  Ajax function for upload Articulate course
		 */
		public static function import_articulate_course() {

			$result = false;
			if ( ! isset( $_POST['post_id'] )
				|| ! isset( $_POST['nonce'] )
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'articulate_load' )
			) {
				die( 'Error in request' );
			}
			if ( ! function_exists( 'unzip_file' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				WP_Filesystem();
			}

			$file_path = null;
			if ( isset( $_FILES['course_file'] ) && ! empty( $_FILES['course_file']['tmp_name'] ) ) {
				// @codingStandardsIgnoreStart
				$file_path = sanitize_text_field(  $_FILES['course_file']['tmp_name']  );
				// @codingStandardsIgnoreEnd
			}
			$post_id    = (int) sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
			$upload_dir = wp_get_upload_dir();
			if ( $file_path ) {
				$pathinfo       = pathinfo( $file_path );
				$unzip_dir_path = $upload_dir['basedir'] . '/' . $pathinfo['filename'];

				mkdir( $unzip_dir_path );
				$result = unzip_file( $file_path, $unzip_dir_path );

				if ( is_wp_error( $result ) ) {
					die( 'Error unzip file' );
				}

				$sub_dirs    = glob( $unzip_dir_path . '/*' );
				$course_path = 1 === count( $sub_dirs ) ? array_pop( $sub_dirs ) : $unzip_dir_path;

			} elseif ( isset( $_POST['dir'] ) ) {
				$course_path    = sanitize_text_field( wp_unslash( $_POST['dir'] ) );
				$unzip_dir_path = dirname( $course_path ) !== $upload_dir['basedir'] ? dirname( $course_path ) : $course_path;
			}

			if ( ! empty( $course_path ) ) {
				$name   = basename( $course_path );
				$result = self::upload_azure_articulate_course( $course_path, $name );
			}

			if ( $result && $result['url'] ) {
				update_post_meta( $post_id, 'course_link_temp', $result['url'] );
				$result['url'] = null;
			}

			if ( $result && ! $result['more'] ) {
				$url = get_post_meta( $post_id, 'course_link_temp', true );
				if ( $url ) {
					update_field( 'course_link', $url, $post_id );
					$result['url'] = $url;
				}
				$wp_filesystem->rmdir( $unzip_dir_path, true );
				// @codingStandardsIgnoreStart
				@unlink( $file_path );
				// @codingStandardsIgnoreEnd
			}

			die( wp_json_encode( $result ) );
		}

		/**
		 *  Upload to Azure storage Articulate course
		 *
		 * @param string $dir folder path.
		 *
		 * @return array
		 */
		public static function list_folder_files( $dir ) {
			$last = substr( $dir, -1 );
			if ( '/' !== $last && '\\' !== $last ) {
				$dir .= '/';
			}

			$ffs = glob( $dir . '*' );

			if ( count( $ffs ) < 1 ) {
				return array();
			}

			$list = array();
			foreach ( $ffs as $ff ) {
				if ( is_dir( $ff ) ) {
					$list = array_merge( $list, self::list_folder_files( $ff ) );
				} else {
					$list[] = $ff;
				}
			}
			return $list;
		}

		/**
		 *  Upload to Azure storage articulate course
		 *
		 * @param string $dir_path folder path.
		 * @param string $name root folder name.
		 *
		 * @return array
		 */
		public static function upload_azure_articulate_course( $dir_path, $name ) {
			$azure_storage_account_name = self::$azure_storage_account_name;
			$azure_storage_account_key  = self::$azure_storage_account_key;
			$azure_storage_container    = self::$azure_storage_container;

			if ( empty( $azure_storage_account_name ) || empty( $azure_storage_account_key ) || empty( $azure_storage_container ) ) {
				die( 'Error in Azure credential' );
			}

			$connection_string = "DefaultEndpointsProtocol=https;AccountName=$azure_storage_account_name;AccountKey=$azure_storage_account_key";
			$blob_client       = BlobRestProxy::createBlobService( $connection_string );

			$dir_path = str_replace( '\\', '/', $dir_path );
			$list     = self::list_folder_files( $dir_path );
			$result   = array(
				'more' => false,
				'url'  => null,
			);

			foreach ( $list as $i => $item ) {

				if ( file_exists( $item ) ) {
					try {
						$pathinfo   = pathinfo( $item );
						$local_path = str_replace( $dir_path, '', $item );

						if ( $name ) {
							$local_path = "$name$local_path";
						}
						// @codingStandardsIgnoreStart
						$content = fopen( $item, 'r' );
						// @codingStandardsIgnoreEnd
						if ( 'js' === $pathinfo['extension'] ) {
							$mime = 'application/javascript';
						} elseif ( 'css' === $pathinfo['extension'] ) {
							$mime = 'text/css';
						} elseif ( 'woff' === $pathinfo['extension'] ) {
							$mime = 'font/woff';
						} else {
							$mime = mime_content_type( $item );
						}

						$opts = null;

						if ( $mime ) {
							$opts = new CreateBlockBlobOptions();
							$opts->setContentType( $mime );
						}

						$blob_client->createBlockBlob( $azure_storage_container, $local_path, $content, $opts );

						if ( in_array(basename( $item ), self::$main_file, true )) {
							$result['url'] = $blob_client->getBlobUrl( $azure_storage_container, $local_path );
						}
						if (!$result['url']) {
              $result['url'] = $blob_client->getBlobUrl( $azure_storage_container, $local_path );
            }

						unlink( $item );
						if ( $i > self::$max_files ) {
							$result['more'] = true;
							$result['dir']  = $dir_path;
							break;
						}
					} catch ( ServiceException $e ) {
						$code          = $e->getCode();
						$error_message = $e->getMessage();
						die( esc_html( $code . ': ' . $error_message . PHP_EOL ) );
					}
				}
			}

			return $result;
		}

	}

	new NYBC_Articulate();
}
