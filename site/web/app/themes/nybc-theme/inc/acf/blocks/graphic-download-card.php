<?php
/**
 * NYBC Graphic Download Card fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61c471aa84edf',
			'title'                 => 'Graphic Download Card',
			'fields'                => array(
				array(
					'key'               => 'field_61c471aae8ac1',
					'label'             => 'Title',
					'name'              => 'title',
					'type'              => 'text',
					'instructions'      => 'This field is required',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
				),
				array(
					'key'               => 'field_61c471aae86de',
					'label'             => 'Image',
					'name'              => 'image',
					'type'              => 'image',
					'instructions'      => 'This field is required',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'array',
					'preview_size'      => 'thumbnail',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
				),
				array(
					'key'               => 'field_61c471aae8ea8',
					'label'             => 'Downloads',
					'name'              => 'downloads',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'collapsed'         => '',
					'min'               => 1,
					'max'               => 0,
					'layout'            => 'table',
					'button_label'      => 'Add Card',
					'sub_fields'        => array(
						array(
							'key'               => 'field_61c472086e413',
							'label'             => 'Label',
							'name'              => 'label',
							'type'              => 'text',
							'instructions'      => 'This field is required',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
						),
						array(
							'key'               => 'field_61c4725c6e414',
							'label'             => 'Media',
							'name'              => 'media',
							'type'              => 'file',
							'instructions'      => 'This field is required',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'url',
							'library'           => 'all',
							'min_size'          => '',
							'max_size'          => '',
							'mime_types'        => '',
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/graphic-download-card',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		)
	);

endif;
