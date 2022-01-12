<?php
/**
 * NYBC Carousel Video fields
 *
 * @file
 * @package NYBC
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_61cdda0408777',
			'title'                 => 'Carousel Video',
			'fields'                => array(
				array(
					'key'               => 'field_61cdda046f853',
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
							'key'               => 'field_61dd58c291439',
							'label'             => 'Video File',
							'name'              => 'file',
							'type'              => 'file',
							'instructions'      => 'Url or Video File must be present',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'url',
							'library'           => 'all',
							'min_size'          => '',
							'max_size'          => '',
							'mime_types'        => 'mp4',
						),
						array(
							'key'               => 'field_61dd5d0ee5f3b',
							'label'             => 'Media File Preview Image',
							'name'              => 'file_image',
							'type'              => 'image',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'array',
							'preview_size'      => 'thumbnail',
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
							'key'               => 'field_61cdda3396e4a',
							'label'             => 'Video',
							'name'              => 'video',
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
								0 => 'group_61ba04e063b6f',
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
						'value'    => 'acf/carousel-video',
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
