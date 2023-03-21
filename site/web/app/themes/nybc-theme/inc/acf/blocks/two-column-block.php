<?php
/**
 * NYBC Two Column block fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61cc80fbdc778',
			'title'                 => 'Two Column block',
			'fields'                => array(
				array(
					'key'               => 'field_61cc812303ef5',
					'label'             => 'Decor',
					'name'              => 'decor',
					'type'              => 'true_false',
					'instructions'      => 'Enable decoration',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => '',
					'default_value'     => 0,
					'ui'                => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				),
				array(
					'key' => 'field_64120ff12b401',
					'label' => 'Hide Breadcrumbs?',
					'name' => 'breadcrumbs',
					'type' => 'true_false',
					'instructions' => 'Check to hide breadcrumbs',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/two-column-block',
					),
				),
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/one-column-block',
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