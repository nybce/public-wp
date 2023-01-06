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
   		<?php
   		$front_hero = get_field('front_hero');
   		if(!empty($front_hero)):
   		?>
   		<h1><?php echo $front_hero['title']; ?></h1>
   		<h2><?php echo $front_hero['subtitle']; ?></h2>
   		<?php	
   		else:
   		?>
	     <h1>Purpose-First Investing</h1>
	     <h2>A venture fund dedicated to accelerating innovations in blood and cell-based therapies & technologies</h2>
	   <?php endif; ?>
   	</div>
    </div>
    <div class="front-addendum">
    	<?php
    	$highlight_text = get_field('highlight_text');
    	if(!empty($highlight_text)):
    	?>
    	<h2><?php echo $highlight_text ?></h2>
    	<?php else: ?>
    	<h2>NYBC Ventures is focused on a humanitarian & fiscal ROI, fostering New York Blood Center's mission-oriented innovation.</h2>
    	<?php endif; ?>
    </div>
</div>
</div>

<div class="nybcv-wrapper">

  <div class="container">
	<div class="nybcv-block">
		<?php $aof = get_field('areas_of_focus'); ?>
		<div class="nybcv-block--intro">
			<?php 
			if(!empty($aof['title'])):
			?>
			<h3><?php echo $aof['title'] ?></h3>
			<?php echo $aof['introduction_text'] ?>
		<?php else: ?>
			<h3>Areas of Focus</h3>
			<p>We invest in therapeutics, devices and innovative technologies addressing the most pressing challenges in our areas of focus.</p>
		<?php endif; ?>
		</div>
		<div class="img-grid">
			<?php
			if(!empty($aof['aof_card_list'])):

    	foreach($aof['aof_card_list'] as $aofc):
        $aofc_title = $aofc['title'];
        $aofc_bg = $aofc['background_image'];
        $aofc_align = $aofc['alignment'];
        $aofc_color = $aofc_align == 'bot-right' ? 'white' : '';
			?>
			<div class="img-grid--square img-grid--square--<?php echo $aofc_align ?>" style="background-image:url(<?php echo $aofc_bg['url'] ?>)">
				<h5 class="<?php echo $aofc_color ?>"><?php echo $aofc_title?></h5>
			</div>
		<?php 
			endforeach;
	else: 
		?>
			<div class="img-grid--square img-grid--square--top-left" style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_ct.jpg)">
				<h5>Cell Therapy</h5>
			</div>
			<div class="img-grid--square img-grid--square--bot-right"  style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_tm.png)">
				<h5 class="white">Transfusion Medicine</h5>
			</div>
			<div class="img-grid--square img-grid--square--bot-right"  style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_bh.png)">
				<h5 class="white">Benign Hematology</h5>
			</div>
			<div class="img-grid--square img-grid--square--top-left"  style="background-image:url(<?php echo get_template_directory_uri(); ?>/src/images/home/aoi/nybcv_ai_id.jpg)">
				<h5>Infectious <br>Disease</h5>
			</div>
		<?php endif; ?>
		</div>
	</div>
</div>
</div>

<div class="nybcv-wrapper">
  <div class="container">
	<div class="nybcv-block">
		<?php $kis = get_field('key_investment_strategies'); ?>
		<div class="nybcv-block--intro">
			<?php if (!empty($kis['title'])): ?>
				<h3><?php echo $kis['title'] ?></h3>
				<?php echo $kis['introduction_text'] ?>
			<?php else: ?>
			<h3>KEY INVESTMENT STRATEGIES</h3>
			<p>Working with NYBC Ventures goes beyond capital. We provide access to world-renowned researchers and their labs, cell manufacturing capacity, and source materials.</p>
		<?php endif; ?>
		</div>
		<div class="card-grid">
			<?php if(!empty($kis['kis_card_list'])): 
				foreach($kis['kis_card_list'] as $kisc):
					$kisc_bg = $kisc['background_image'];
				?>
			<div class="card-grid--card">
				<div class="card-grid--card--bg" style="background-image:url(<?php echo $kisc_bg['url'] ?>)">
					<h5><?php echo $kisc['title'] ?></h5>
					<p><?php echo $kisc['description'] ?></p>
				</div>
			</div>
			<?php 
		endforeach;
		else: ?>
			<div class="card-grid--card">
				<div class="card-grid--card--bg  card-grid--card--bg01">
					<h5>EXTERNAL INNOVATIONS</h5>
					<p>Early-stage companies pursuing advancements in blood-related and cellular therapeutics & technologies</p>
				</div>
			</div>
			<div class="card-grid--card">
				<div class="card-grid--card--bg  card-grid--card--bg02">
					<h5>JOINT <br class="desktop-only">VENTURE</h5>
					<p>Companies that would benefit from a partnership with Lindsley F. Kimball Research Institute (LFKRI) researchers  and cell manufacturing and sourcing capabilities</p>
				</div>
			</div>
			<div class="card-grid--card">
				<div class="card-grid--card--bg  card-grid--card--bg03">
					<h5>INTERNAL INNOVATIONS</h5>
					<p>Discoveries emerging from New York Blood Center research labs</p>
				</div>
			</div>
		<?php endif; ?>
		</div>
	</div>
</div>
</div>

</div>
<?php get_footer();
