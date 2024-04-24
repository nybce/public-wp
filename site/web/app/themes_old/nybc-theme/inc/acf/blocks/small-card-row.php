<?php
/**
 * NYBC Small Card Row fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61b89952d1fa6',
			'title'                 => 'Small Card Row',
			'fields'                => array(
				array(
					'key'               => 'field_61b89969d799e',
					'label'             => 'Cards',
					'name'              => 'cards',
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
					'min'               => 1,
					'max'               => 0,
					'layout'            => 'block',
					'button_label'      => 'Add Card',
					'sub_fields'        => array(
						array(
							'key'               => 'field_61b89982d799f',
							'label'             => 'Card',
							'name'              => 'card',
							'type'              => 'clone',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'clone'             => array(
								0 => 'group_61a4efad7ffdc',
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
						'value'    => 'acf/small-card-row',
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
			'show_in_rest'          => false,
		)
	);

endif;
