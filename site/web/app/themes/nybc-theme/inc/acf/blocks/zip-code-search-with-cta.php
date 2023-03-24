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
					'label'             => 'Zip Code Title',
					'name'              => 'title',
					'type'              => 'text',
					'instructions'      => 'This field is required.',
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
					'label'             => 'Zip Code Subtitle',
					'name'              => 'input_label',
					'type'              => 'text',
					'instructions'      => 'Subtitle beneath Zipcode Title',
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
					'label'             => 'Zip Code Search Link',
					'name'              => 'link',
					'type'              => 'link',
					'instructions'      => 'This field is required and should link to an external donation page ready to receive a Zip Code, eg. https://donate.nybc.org/donor/schedules/zip/',
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
					'label'             => 'Post-Search Title',
					'name'              => 'description_title',
					'type'              => 'text',
					'instructions'      => 'This field is required. Title for additional text under the Zip Code Search.',
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
					'label'             => 'Post-Search Description',
					'name'              => 'description',
					'type'              => 'text',
					'instructions'      => 'This field is required. Description for additional text under the Zip Code Search.',
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
					'label'             => 'CTA Title',
					'name'              => 'title_cta',
					'type'              => 'text',
					'instructions'      => 'This field is required. Title for additional CTA block that displays at the side of the Zip Code Search.',
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
					'label'             => 'CTA Description',
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
					'label'             => 'CTA Link',
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
