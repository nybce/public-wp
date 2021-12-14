<?php
/**
 * NYBC Callout with CTA Carousel fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61a4e52014e0e',
			'title'                 => 'Callout with CTA Carousel',
			'fields'                => array(
				array(
					'key'               => 'field_61a4e569353a1',
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
					'collapsed'         => '',
					'min'               => 2,
					'max'               => 0,
					'layout'            => 'block',
					'button_label'      => 'Add Slide',
					'sub_fields'        => array(
						array(
							'key'               => 'field_61a4ff51627fc',
							'label'             => 'slide',
							'name'              => 'slide',
							'type'              => 'clone',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'clone'             => array(
								0 => 'group_61a4fee8df835',
							),
							'display'           => 'seamless',
							'layout'            => 'block',
							'prefix_label'      => 0,
							'prefix_name'       => 0,
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/callout-with-cta-carousel',
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
