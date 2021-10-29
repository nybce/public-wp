<?php
// Load WP Boilerplate Sass styles
function boilerplatesass_styles()
{
  // Normalize is loaded in boilerplate-theme and both are imported into the style.css via Sass
  wp_register_style('boilerplatesass', get_template_directory_uri() . '/dist/style.min.css', array(), '1.0.0', 'all');
  wp_enqueue_style('boilerplatesass'); // Enqueue it!
}

add_action('wp_enqueue_scripts', 'boilerplatesass_styles'); // Add Theme Stylesheet

