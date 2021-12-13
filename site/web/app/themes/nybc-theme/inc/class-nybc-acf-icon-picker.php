<?php
/**
 * NYBC ACF icon picker
 *
 * @file
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NYBC_Acf_Icon_Picker' ) && class_exists( 'acf_field' ) ) {

	/**
	 * ACF icon picker class
	 *
	 * @category Class
	 * @package NYBC
	 */
	class NYBC_Acf_Icon_Picker extends acf_field {
		/**
		 *  NYBC_Acf_Icon_Picker constructor
		 *
		 * @param array $settings  settings.
		 */
		public function __construct( $settings ) {

			$this->name = 'icon-picker';

			$this->label = __( 'Icon Picker', 'acf-icon-picker' );

			$this->category = 'jquery';

			$this->defaults = array(
				'initial_value' => '',
			);

			$this->l10n = array(
				'error' => __( 'Error!', 'acf-icon-picker' ),
			);

			$this->settings = $settings;

			$this->path_suffix = $this->settings['path_suffix'];

			$this->path = apply_filters( 'acf_icon_path', $this->settings['path'] ) . $this->path_suffix;

			$this->url = apply_filters( 'acf_icon_url', $this->settings['url'] ) . $this->path_suffix;

			$priority_dir_lookup = get_stylesheet_directory() . '/' . $this->path_suffix;

			if ( file_exists( $priority_dir_lookup ) ) {
				$this->path = $priority_dir_lookup;
				$this->url  = get_stylesheet_directory_uri() . '/' . $this->path_suffix;
			}

			$this->svgs = array();

			$files = array_diff( scandir( $this->path ), array( '.', '..' ) );

			foreach ( $files as $file ) {
				if ( 'svg' === pathinfo( $file, PATHINFO_EXTENSION ) ) {
					$exploded = explode( '.', $file );
					$icon     = array(
						'name' => $exploded[0],
						'icon' => $file,
					);
					array_push( $this->svgs, $icon );
				}
			}
			parent::__construct();
		}

		/**
		 *  Render field method
		 *
		 * @param array $field field options.
		 */
		public function render_field( $field ) {
			$input_icon = '' === $field['value'] ? $field['value'] : $field['initial_value'];
			$svg        = $this->path . $input_icon . '.svg';
			?>
			<div class="acf-icon-picker">
				<div class="acf-icon-picker__img">
					<?php
					if ( file_exists( $svg ) ) {
						$svg = $this->url . $input_icon . '.svg';
						?>
						<div class="acf-icon-picker__svg">
							<img src="<?php echo esc_url( $svg ); ?>" alt=""/>
						</div>;
					<?php } else { ?>
						<div class="acf-icon-picker__svg">
							<span class="acf-icon-picker__svg--span">&plus;</span>
						</div>
					<?php } ?>
					<input type="hidden" readonly name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $input_icon ); ?>"/>
				</div>
					<?php if ( false === $field['required'] ) { ?>
					<span class="acf-icon-picker__remove">
						Remove
					</span>
				<?php } ?>
			</div>
			<?php
		}

		/**
		 *  Enqueue admin scripts
		 */
		public function input_admin_enqueue_scripts() {

			$version = $this->settings['version'];

			wp_register_script( 'acf-input-icon-picker', NYBC_LIB_URI . '/js/nybc-acf-icon-picker.js', array( 'acf-input' ), $version, true );
			wp_enqueue_script( 'acf-input-icon-picker' );

			wp_localize_script(
				'acf-input-icon-picker',
				'iv',
				array(
					'path'         => $this->url,
					'svgs'         => $this->svgs,
					'no_icons_msg' => sprintf( 'To add icons, add your svg files in the /%s folder in your theme.', $this->path_suffix ),
				)
			);

			wp_register_style( 'acf-input-icon-picker', NYBC_LIB_URI . '/css/nybc-acf-icon-picker.css', array( 'acf-input' ), $version );
			wp_enqueue_style( 'acf-input-icon-picker' );
		}
	}
	/**
	 *  Add field to ACF
	 */
	add_action(
		'acf/include_field_types',
		function () {
			new NYBC_Acf_Icon_Picker(
				array(
					'version'     => '1.0.0',
					'path_suffix' => 'img/picker-icons/',
					'url'         => NYBC_THEME_URI,
					'path'        => NYBC_THEME_DIR,
				)
			);
		}
	);

}
