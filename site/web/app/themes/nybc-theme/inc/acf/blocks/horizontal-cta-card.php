<?php
/**
 * NYBC Large Card fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61b8c4219f517',
			'title'                 => 'Horizontal CTA Card',
			'fields'                => array(
				array(
					'key'               => 'field_61b8c48764db6',
					'label'             => 'Image',
					'name'              => 'image',
					'type'              => 'image',
					'instructions'      => 'This field is required',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'array',
					'preview_size'      => 'medium',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
				),
				array(
					'key'               => 'field_61b8c43e64db5',
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
					'default_value'     => 'Horizontal CTA Card',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
				),
				array(
					'key'               => 'field_61b8c4ba64db7',
					'label'             => 'Content',
					'name'              => 'content',
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
					'key'               => 'field_61b8c4ef64db8',
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
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/horizontal-cta-card',
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
