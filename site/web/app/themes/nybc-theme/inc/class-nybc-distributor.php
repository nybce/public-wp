<?php
/**
 * NYBC Distributor
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Distributor' ) ) {
	/**
	 * NYBC Distributor class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Distributor {


		/**
		 * NYBC Distributor constructor
		 */
		public function __construct() {
			add_action( 'dt_push_post', array( 'NYBC_Distributor', 'push_post' ), 10, 4 );
			add_action( 'dt_pull_post', array( 'NYBC_Distributor', 'pull_post' ), 10, 3 );
		}

		/**
		 *  Get media ID by file name
		 *
		 * @param string $name file name.
		 *
		 * @return integer
		 */
		public static function get_media_by_name( $name ) {
			global $wpdb;

			if ( false === strpos( $name, '.' ) ) {
				$name .= '.';
			}

			$query = "SELECT post_id  FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE '%/$name%'";
			// @codingStandardsIgnoreStart
			$file_id = $wpdb->get_var( $query );
			// @codingStandardsIgnoreEnd
			return $file_id ? (int) $file_id : null;

		}

		/**
		 *  Move media files in blocks
		 *
		 * @param array   $blocks blocks data.
		 * @param integer $destination_blog_id destination site id.
		 *
		 * @return array
		 */
		public static function move_media( $blocks, $destination_blog_id ) {
			foreach ( $blocks as $i => $block ) {

				if ( 0 !== strpos( $blocks[ $i ]['blockName'], 'acf/' ) ) {
					continue;
				}

				if ( isset( $blocks[ $i ]['innerBlocks'] ) && ! empty( $blocks[ $i ]['innerBlocks'] ) ) {
					$blocks[ $i ]['innerBlocks'] = self::move_media( $blocks[ $i ]['innerBlocks'], $destination_blog_id );
				}
				if ( isset( $blocks[ $i ]['attrs'] ) && ! empty( $blocks[ $i ]['attrs'] ) && isset( $blocks[ $i ]['attrs']['data'] ) && ! empty( $blocks[ $i ]['attrs']['data'] ) ) {
					foreach ( $blocks[ $i ]['attrs']['data'] as $name => $data ) {

						if ( ! is_int( $blocks[ $i ]['attrs']['data'][ $name ] ) ) {
							continue;
						}

						$attachment_url = wp_get_attachment_url( $blocks[ $i ]['attrs']['data'][ $name ] );

						if ( ! $attachment_url ) {
							continue;
						}

						switch_to_blog( $destination_blog_id );

						$file_name = basename( $attachment_url );

						$destination_attachment_id = self::get_media_by_name( $file_name );

						if ( empty( $destination_attachment_id ) ) {

							$tmp                       = download_url( $attachment_url );
							$file_array                = array(
								'name'     => $file_name,
								'tmp_name' => $tmp,
							);
							$destination_attachment_id = media_handle_sideload( $file_array );
						}
						if ( ! empty( $destination_attachment_id ) && ! is_wp_error( $destination_attachment_id ) ) {
							$blocks[ $i ]['attrs']['data'][ $name ] = $destination_attachment_id;
						}

						restore_current_blog();
					}
				}
			}
			return $blocks;
		}

		/**
		 * Move media files on push
		 *
		 * @param int    $destination_post_id The newly created post ID.
		 * @param int    $original_post_id The original post ID.
		 * @param array  $args Not used (The arguments passed into wp_insert_post).
		 * @param object $site The distributor connection being pulled from.
		 */
		public static function push_post( $destination_post_id, $original_post_id, $args, $site ) {

			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$destination_blog_id = ( is_numeric( $site ) ) ? $site : $site->site->blog_id;

			restore_current_blog();

			if ( ! has_blocks( $original_post_id ) ) {
				return;
			}

			$original_content = get_the_content( null, null, $original_post_id );
			$original_blocks  = parse_blocks( $original_content );
			if ( ! $original_blocks || ! is_array( $original_blocks ) ) {
				return;
			}

			$destination_blocks = self::move_media( $original_blocks, $destination_blog_id );

			$destination_content = serialize_blocks( $destination_blocks );

			switch_to_blog( $destination_blog_id );

			wp_update_post(
				wp_slash(
					array(
						'ID'           => $destination_post_id,
						'post_content' => $destination_content,
					)
				)
			);

		}

		/**
		 * Move media files on pull
		 *
		 * @param int   $destination_post_id Newly created post id.
		 * @param array $args hook args.
		 * @param array $post_array post data.
		 */
		public static function pull_post( $destination_post_id, $args, $post_array ) {

			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$destination_blog_id = get_current_blog_id();

			$original_blog_id = isset( $args->site ) ? (int) $args->site->blog_id : null;
			$original_content = ! empty( $post_array['post_content'] ) ? $post_array['post_content'] : null;

			if ( ! $original_blog_id || ! $original_content ) {
				return;
			}

			if ( ! has_blocks( $original_content ) ) {
				return;
			}

			$original_blocks = parse_blocks( $original_content );

			if ( ! $original_blocks || ! is_array( $original_blocks ) ) {
				return;
			}

			switch_to_blog( $original_blog_id );

			$destination_blocks = self::move_media( $original_blocks, $destination_blog_id );

			$destination_content = serialize_blocks( $destination_blocks );

			restore_current_blog();

			wp_update_post(
				wp_slash(
					array(
						'ID'           => $destination_post_id,
						'post_content' => $destination_content,
					)
				)
			);
		}

	}

	new NYBC_Distributor();
}
