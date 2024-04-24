<?php
/**
 * NYBC Custom Horizontal Line
 *
 * @file
 * @package NYBC
 */

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_63fd7bd4b2216',
		'title' => 'Custom Horizontal Line',
		'fields' => array(
			array(
				'key' => 'field_63fd7c400cc7a',
				'label' => 'Thickness',
				'name' => 'thickness',
				'type' => 'range',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 3,
				'min' => 1,
				'max' => 10,
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_63fd7cab0cc7b',
				'label' => 'Color',
				'name' => 'color',
				'type' => 'color_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '#C8D8E8',
				'enable_opacity' => 0,
				'return_format' => 'string',
			),
			array(
				'key' => 'field_63fd7cd20cc7c',
				'label' => 'Top Spacing',
				'name' => 'top_spacing',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 24,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'min' => '',
				'max' => '',
				'step' => '',
			),
			array(
				'key' => 'field_63fd7d1d0cc7d',
				'label' => 'Bottom Spacing',
				'name' => 'bottom_spacing',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 24,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'min' => '',
				'max' => '',
				'step' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/custom-horizontal-line',
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
		'show_in_rest' => 0,
		'acfe_display_title' => '',
		'acfe_autosync' => '',
		'acfe_form' => 0,
		'acfe_meta' => '',
		'acfe_note' => '',
	));

endif;		


