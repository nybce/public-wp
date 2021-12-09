<?php
/**
 * The header for our theme
 *
 * @package NYBC
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
	<meta name='robots' content='noindex,nofollow' />
	<link rel="shortcut icon" href="<?php esc_url( NYBC_IMG_URI ); ?>/favicon.ico" />

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="loader-wrapper"></div>
	<div id="content-block">
		<header>
		</header>
