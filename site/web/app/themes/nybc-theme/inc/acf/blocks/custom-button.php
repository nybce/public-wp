<?php
/**
 * NYBC Custom Button
 *
 * @file
 * @package NYBC
 */

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_63c1b907b48cd',
	'title' => 'Custom Button',
	'fields' => array(
		array(
			'key' => 'field_63c1b90e7e65b',
			'label' => 'Link',
			'name' => 'link',
			'type' => 'acfe_advanced_link',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => '',
			'taxonomy' => '',
		),
		array(
			'key'               => 'field_63c1b90e7e76d',
			'label'             => 'Alignment',
			'name'              => 'alignment',
			'type'              => 'select',
			'instructions'      => 'This field is required',
			'required'          => 1,
			'conditional_logic' => 0,
			'choices'           => array(
				'Left' 			=> 'Left',
				'Center' 		=> 'Center',
				'Right' 		=> 'Right'
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
			'key'               => 'field_63c1b90e7e77d',
			'label'             => 'Button Type',
			'name'              => 'type',
			'type'              => 'select',
			'instructions'      => 'This field is required',
			'required'          => 1,
			'conditional_logic' => 0,
			'choices'           => array(
				'big-blue' 		=> 'big blue button',
				'small-white' 	=> 'small white button'
				
			),
			'default_value'     => false,
			'allow_null'        => 0,
			'multiple'          => 0,
			'ui'                => 0,
			'return_format'     => 'value',
			'ajax'              => 0,
			'placeholder'       => '',
		)
	),
	'location'              => array(
		array(
			array(
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/custom-button',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'left',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'acfe_display_title' => '',
	'acfe_autosync' => '',
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
));

endif;		


