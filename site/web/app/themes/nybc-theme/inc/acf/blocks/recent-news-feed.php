<?php
/**
 * NYBC Recent News Feed fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61b3491bc8e57',
			'title'                 => 'Recent News Feed',
			'fields'                => array(
				array(
					'key'               => 'field_61b3492edc5a0',
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
					'default_value'     => 'Recent News Feed',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
				),
				array(
					'key'               => 'field_61b3497fdc5a2',
					'label'             => 'Recent News Count',
					'name'              => 'count',
					'type'              => 'number',
					'instructions'      => '',
					'required'          => 0,
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
					'min'               => 1,
					'max'               => '',
					'step'              => 1,
				),
				array(
					'key'               => 'field_61b3494edc5a1',
					'label'             => 'News',
					'name'              => 'news',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_61b3497fdc5a2',
								'operator' => '==empty',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'post_type'         => array(
						0 => 'post',
					),
					'taxonomy'          => '',
					'allow_null'        => 0,
					'multiple'          => 1,
					'return_format'     => 'id',
					'ui'                => 1,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/recent-news-feed',
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
