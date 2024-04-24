<?php
/**
 * NYBC Employee Spotlight Carousel fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61a5f8e81f1d4',
			'title'                 => 'Employee Spotlight Carousel',
			'fields'                => array(
				array(
					'key'               => 'field_61a5f9653f1b9',
					'label'             => 'Employee Spotlight',
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
					'collapsed'         => 'field_61a5f9a43f1ba',
					'min'               => 2,
					'max'               => 0,
					'layout'            => 'table',
					'button_label'      => 'Add Employee',
					'sub_fields'        => array(
						array(
							'key'               => 'field_61a5f9a43f1ba',
							'label'             => 'Employee',
							'name'              => 'employee',
							'type'              => 'post_object',
							'instructions'      => 'This field is required',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'post_type'         => array(
								0 => 'staff',
							),
							'taxonomy'          => '',
							'allow_null'        => 0,
							'multiple'          => 0,
							'return_format'     => 'object',
							'ui'                => 1,
						),
						array(
							'key'               => 'field_61a5f9e53f1bb',
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
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/employee-spotlight-carousel',
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
		)
	);

endif;
