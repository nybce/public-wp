<?php
/**
 * NYBC Tabbed Card Carousel fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61a4dce4bf69d',
			'title'                 => 'Tabbed Card Carousel',
			'fields'                => array(
				array(
					'key'               => 'field_61a4dd25a6f1e',
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
					'key'               => 'field_61a4dd31a6f1f',
					'label'             => 'Content',
					'name'              => 'content',
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
					'key'               => 'field_61a4dd3da6f20',
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
					'key'               => 'field_61a4dd69a6f21',
					'label'             => 'Tab',
					'name'              => 'tab',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'collapsed'         => 'field_61a4dd84a6f22',
					'min'               => 1,
					'max'               => 0,
					'layout'            => 'block',
					'button_label'      => '',
					'sub_fields'        => array(
						array(
							'key'               => 'field_61a4dd84a6f22',
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
							'key'               => 'field_61a4f31433798',
							'label'             => 'Small Cards',
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
							'layout'            => 'table',
							'button_label'      => 'Add Card',
							'sub_fields'        => array(
								array(
									'key'               => 'field_61a4f3c033799',
									'label'             => 'Card',
									'name'              => 'card',
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
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/tabbed-card-carousel',
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
