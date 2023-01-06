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
    '172.29.0.1',
    '::1',
    '::3000',
    '::80'
);

if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)): ?>
  <link rel="shortcut icon" href="<?php echo HOME_URI ?>wp-content/themes/nybc-venture/src/images/favicon.ico" />
  <link rel='stylesheet' id='theme-css'  href='<?php echo HOME_URI ?>wp-content/themes/nybc-venture/css/theme.min.css' type='text/css' media='all' />
<?php
else:
?>
  <link rel="shortcut icon" href="<?php echo HOME_URI ?>app/themes/nybc-venture/src/images/favicon.ico" />
  <link rel='stylesheet' id='theme-css'  href='<?php echo HOME_URI ?>app/themes/nybc-venture/css/theme.min.css' type='text/css' media='all' />
<?php endif; ?>

<?php

$ga_code = get_field("ga_code", "options");

if($ga_code){
?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $ga_code ?>');</script>
<!-- End Google Tag Manager -->
<?php
}else{
echo "<!-- No Google Tag in Use -->";
}
?>
</head>

<body <?php body_class(); ?>>
<?php

if($ga_code){
?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $ga_code ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php
}else{
echo "<!-- No Google Tag in Use -->";
}
?>  
  <?php wp_body_open(); ?>
  <?php get_template_part('template-parts/global/header', null, null); ?>
  <main>