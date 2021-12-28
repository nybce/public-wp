<?php
/**
 * NYBC Donor Stories Carousel fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61bca2929013f',
			'title'                 => 'Donor Stories Carousel',
			'fields'                => array(
				array(
					'key'               => 'field_61bca293002d1',
					'label'             => 'Slides',
					'name'              => 'slides',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'collapsed'         => 'field_61bca2931c205',
					'min'               => 2,
					'max'               => 0,
					'layout'            => 'block',
					'button_label'      => 'Add Slide',
					'sub_fields'        => array(
						array(
							'key'               => 'field_61bca2931c205',
							'label'             => 'Quote',
							'name'              => 'quote',
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
							'key'               => 'field_61bca2fac36c2',
							'label'             => 'Text',
							'name'              => 'text',
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
							'key'               => 'field_61bca329c36c3',
							'label'             => 'Link',
							'name'              => 'link',
							'type'              => 'link',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'array',
						),
						array(
							'key'               => 'field_61bca348c36c4',
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
							'preview_size'      => 'medium',
							'library'           => 'all',
							'min_width'         => '',
							'min_height'        => '',
							'min_size'          => '',
							'max_width'         => '',
							'max_height'        => '',
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
						'value'    => 'acf/donor-stories-carousel',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
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
