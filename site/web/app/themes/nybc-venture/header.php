<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width" />
  <?php wp_head(); ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- might have to change depending on local or staging or prod -->
  <?php 

  $whitelist = array(
    '127.0.0.1',
    '::1'
);

if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)): ?>

  <link rel='stylesheet' id='theme-css'  href='<?php echo HOME_URI ?>wp-content/themes/nybc-venture/css/theme.min.css' type='text/css' media='all' />
<?php
else:
?>
  <link rel='stylesheet' id='theme-css'  href='<?php echo HOME_URI ?>app/themes/nybc-venture/css/theme.min.css' type='text/css' media='all' />
<?php endif; ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <?php get_template_part('template-parts/global/header', null, null); ?>
  <main>