<?php

/*
  Template Name: Contact
  */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

get_header();

$contact_info = get_field('contact_info');

?>
<div class="page-wrapper contact-page">

<div class="nybcv-wrapper">

  <div class="container">
	<div class="nybcv-block contact-block">
		<div class="nybcv-block--intro">
			<h3><?php echo get_field('title')?></h3>
			<?php echo get_field('introduction_text') ?>
		</div>
		<div class="contact-block--spacer"></div>
		<div class="contact-block--info">
			<h4><?php echo $contact_info['address_1']; ?></h4>
			<h5><?php echo $contact_info['address_2']; ?></h5>
			<a href="mailto:<?php echo $contact_info['email']; ?>"><?php echo $contact_info['email']; ?></a>
		</div>
		<div class="contact-block--spacer"></div>
		<div class="contact-block--form">
			<?php echo get_field('contact_form_shortcode') ?>
		</div>
	</div>
</div>
</div>


</div>
<?php get_footer();
