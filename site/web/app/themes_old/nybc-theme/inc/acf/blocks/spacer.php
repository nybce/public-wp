<?php
/**
 * NYBC Spacer fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61bb4ad8695b4',
			'title'                 => 'Spacer',
			'fields'                => array(
				array(
					'key'               => 'field_61bb4af54d93e',
					'label'             => 'Height',
					'name'              => 'height',
					'type'              => 'select',
					'instructions'      => 'This field is required',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '50',
						'class' => '',
						'id'    => '',
					),
					'choices'           => array(
						0   => '0',
						2   => '2',
						4   => '4',
						8   => '8',
						12  => '12',
						16  => '16',
						24  => '24',
						32  => '32',
						40  => '40',
						48  => '48',
						64  => '64',
						72  => '72',
						96  => '96',
						120 => '120',
					),
					'default_value'     => false,
					'allow_null'        => 0,
					'multiple'          => 0,
					'ui'                => 0,
					'return_format'     => 'value',
					'ajax'              => 0,
					'placeholder'       => '',
				),
				array(
					'key'               => 'field_61bb4b124d93f',
					'label'             => 'Height XS',
					'name'              => 'height_xs',
					'type'              => 'select',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '50',
						'class' => '',
						'id'    => '',
					),
					'choices'           => array(
						'none' => 'none',
						0      => '0',
						2      => '2',
						4      => '4',
						8      => '8',
						12     => '12',
						16     => '16',
						24     => '24',
						32     => '32',
						40     => '40',
						48     => '48',
						64     => '64',
						72     => '72',
						96     => '96',
						120    => '120',
					),
					'default_value'     => false,
					'allow_null'        => 0,
					'multiple'          => 0,
					'ui'                => 0,
					'return_format'     => 'value',
					'ajax'              => 0,
					'placeholder'       => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/spacer',
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
