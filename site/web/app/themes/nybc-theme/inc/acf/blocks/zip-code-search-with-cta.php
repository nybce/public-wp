<?php
/**
 * NYBC Zip Code Search with CTA fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61bcb063ce383',
			'title'                 => 'Zip Code Search with CTA',
			'fields'                => array(
				array(
					'key'               => 'field_61bcb0642e82d',
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
					'key'               => 'field_61bcb0642ec0d',
					'label'             => 'Input Label',
					'name'              => 'input_label',
					'type'              => 'text',
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
					'maxlength'         => '',
				),
				array(
					'key'               => 'field_61bcb0642efef',
					'label'             => 'Link',
					'name'              => 'link',
					'type'              => 'link',
					'instructions'      => 'This field is required',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'array',
				),
				array(
					'key'               => 'field_61bcb0642f3db',
					'label'             => 'Description Title',
					'name'              => 'description_title',
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
					'key'               => 'field_61bcb0642f7bd',
					'label'             => 'Description',
					'name'              => 'description',
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
					'key'               => 'field_61bcb0fa40a9b',
					'label'             => 'Title CTA',
					'name'              => 'title_cta',
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
					'key'               => 'field_61bcb10040a9c',
					'label'             => 'Description	CTA',
					'name'              => 'description_cta',
					'type'              => 'wysiwyg',
					'instructions'      => 'This field is required',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'tabs'              => 'all',
					'toolbar'           => 'full',
					'media_upload'      => 0,
					'delay'             => 0,
				),
				array(
					'key'               => 'field_61bcb0f640a9a',
					'label'             => 'Link CTA',
					'name'              => 'link_cta',
					'type'              => 'link',
					'instructions'      => 'This field is required',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'array',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/zip-code-search-with-cta',
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
