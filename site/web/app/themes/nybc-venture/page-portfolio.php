<?php

/*
  Template Name: Portfolio
  */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

get_header();
?>
<div class="page-wrapper portfolio-page">

<div class="nybcv-wrapper">

  <div class="container">
	<div class="nybcv-block">
		<div class="nybcv-block--intro">
			<h3><?php echo get_field('title')?></h3>
			<?php echo get_field('introduction_text') ?>
		</div>

<div class="portfolio-grid">
		<?php

if( have_rows('portfolio') ):

  while( have_rows('portfolio') ) : the_row();

    $logo = get_sub_field('logo');
    $company_title = get_sub_field('company_title');
    $description = get_sub_field('description');
    $link = get_sub_field('company_link');
?>
	<div class="portfolio-grid--square">
		<div class="portfolio-grid--square--logo">
			<?php if($logo): ?>
				<img src="<?php echo $logo['sizes']['large'] ?>" alt="<?php echo $logo['alt'] ?>"/>
			<? else: ?>
				<h2><?php echo $company_title ?></h2>
			<?php endif;?>
		</div>
		<div class="portfolio-grid--square--intro">
			<h4><?php echo $company_title ?></h4>
			<p><?php echo $description ?></p>
			<?php if($link): ?>
				<a href="<?php echo $link['url'] ?>" target="_blank"><?php echo $link['title'] ?></a>
			<?php endif; ?>
		</div>
	</div>
<?php
  endwhile;

// No value.
else :
    // Do something...
endif;
		 ?>
</div>
	</div>
</div>
</div>


</div>
<?php get_footer();
