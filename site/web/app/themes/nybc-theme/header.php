<?php
/**
 * The header for our theme
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
	<meta name='robots' content='noindex,nofollow' />
	<link rel="shortcut icon" href="<?php echo esc_url( NYBC_IMG_URI ); ?>/favicon.ico" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<?php

$ga_code = get_field("ga_code", "options");
$site_type = get_field("type", "options");

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

	<?php wp_head(); ?>

<?php if($site_type == "Division") : ?>
<link rel="stylesheet" id="nybc-division-style-css" href="<?php echo esc_url( NYBC_ASSETS_URI ); ?>/divisions.css" type="text/css" media="all">
<?php endif;?>
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

<div id="loader-wrapper"></div>
	<div id="content-block">
		<header>
			<?php get_template_part( 'template-parts/header/header' ); ?>
		</header>
