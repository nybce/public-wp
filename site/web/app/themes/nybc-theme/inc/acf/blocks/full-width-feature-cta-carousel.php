<?php
/**
 * NYBC Full Width Feature CTA Carousel fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61a4ca5db22bd',
			'title'                 => 'Full Width Feature CTA Carousel',
			'fields'                => array(
				array(
					'key'               => 'field_61a4ca665daa4',
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
					'collapsed'         => 'field_61a4cb375daa6',
					'min'               => 2,
					'max'               => 0,
					'layout'            => 'block',
					'button_label'      => 'Add Slide',
					'sub_fields'        => array(
						array(
							'key'               => 'field_61a4f99e550bf',
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
								0 => 'group_61a4c4ef4ef5c',
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
						'value'    => 'acf/full-width-feature-cta-carousel',
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
