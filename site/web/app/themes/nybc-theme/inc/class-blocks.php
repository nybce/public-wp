<?php
/**
 * NYBC Blocks Init class
 *
 * @package NYBC
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('NYBC_Blocks')) {
    class NYBC_Blocks
    {

        function __construct()
        {
            add_filter('block_categories_all', ['NYBC_Blocks', 'block_categories_all'], 10, 2);

            add_action('acf/init', ['NYBC_Blocks', 'init_block_types']);

            add_filter('allowed_block_types_all', ['NYBC_Blocks', 'allowed_block_types_all'], 100, 2);

        }

        /**
         *  Register allowed block types
         */
        static function allowed_block_types_all($allowed_blocks, $content)
        {

            return [
                'acf/home-hero',
            ];

        }

        /**
         *  Register NYBC blocks category
         */
        static function block_categories_all($block_categories, $editor_context)
        {
            if (!empty($editor_context->post)) {
                array_push(
                    $block_categories,
                    array(
                        'slug' => 'nybc',
                        'title' => esc_html__('NYBC', 'nybc'),
                        'icon' => null,
                    )
                );
            }
            return $block_categories;
        }

        /**
         *  Init NYBC blocks
         */
        static function init_block_types()
        {
            self::home_hero(); //N2RDEV-20   #0110
        }

        /**
         *  Register Home Hero block, N2RDEV-20, #0110
         */
        static function home_hero()
        {
            if (function_exists('acf_register_block_type')) {
                acf_register_block_type([
                    'name' => 'home_hero',
                    'title' => esc_html__('Home Hero', 'nybc'),
                    'description' => esc_html__('Home Hero for Home Page', 'nybc'),
                    'render_template' => 'template-parts/blocks/home-hero/home-hero.php',
                    'category' => 'nybc',
                    'enqueue_style' => NYBC_ASSETS_URI . '/home-hero.css',
                    'supports' => [
                        'multiple' => false,
                        'align' => false
                    ]
                ]);

                /**
                 *  Add block fields
                 */
                get_template_part('inc/acf/blocks/home-hero');
            }
        }

    }

    new NYBC_Blocks();
}
