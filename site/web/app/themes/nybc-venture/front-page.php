<?php

/**
 * Front page
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

get_header();
?>
<div class="page-wrapper">

<div class="hero-wrapper">
<div class="container">
    <div class="front-hero">
    	<div class="front-hero--bg">
    		<div class="front-hero--bg--image-bar"></div>

    		<div class="front-hero--bg--image-circle"></div>
    	</div>
   	<div class="front-hero--text-ctn">
	     <h1>Purpose-First Investing</h1>
	     <h2>A venture fund dedicated to accelerating innovations in blood-related and cellular therapy & technologies</h2>
   	</div>
    </div>
</div>
</div>

<div class="nybcv-wrapper">

  <div class="container">
	<div class="nybcv-block">
		<div class="nybcv-block--intro">
			<h3>Areas of Focus</h3>
			<p>We invest in therapeutics, devices and innovative technologies addressing the most pressing challenges in our areas of focus.</p>
		</div>
		<div class="img-grid">
			<div class="img-grid--square img-grid--square--top-left" style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_ct.jpg)">
				<h5>Cell Therapy</h5>
			</div>
			<div class="img-grid--square img-grid--square--bot-right"  style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_tm.png)">
				<h5>Transfusion Medicine</h5>
			</div>
			<div class="img-grid--square img-grid--square--bot-right"  style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_bh.png)">
				<h5>Benign Hematology</h5>
			</div>
			<div class="img-grid--square img-grid--square--top-left"  style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_id.jpg)">
				<h5>Infectious Disease</h5>
			</div>
		</div>
	</div>
</div>
</div>

<div class="nybcv-wrapper">
  <div class="container">
	<div class="nybcv-block">
		<div class="nybcv-block--intro">
			<h3></h3>
			<p></p>
		</div>
		<div class="card-grid">
			<div class="card-grid--card card-grid--card--bg01">
				<h5></h5>
				<p></p>
			</div>
			<div class="card-grid--card card-grid--card--bg02">
				<h5></h5>
				<p></p>
			</div>
			<div class="card-grid--card card-grid--card--bg03">
				<h5></h5>
				<p></p>
			</div>
		</div>
	</div>
</div>
</div>

</div>
<?php get_footer();
