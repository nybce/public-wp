<?php

/*
  Template Name: About
  */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

get_header();

$hero_image = get_field('hero_image');
$main_column = get_field('main_column');
$title = $main_column['title'];
$content = $main_column['content'];
$sidebar = get_field('sidebar');
$side_main = $sidebar['main_image'];
$side_second = $sidebar['secondary_image'];
?>
<div class="page-wrapper about-page">

<div class="reduced-wrapper">
  <div class="container">
  	<div class="hero-image">
  		<img src="<?php echo $hero_image['url'] ?>"/>
  	</div>
	</div>
</div>

<div class="major-reduced-wrapper">
	<div class="container">
		<div class="two-col">
			<div class="two-col--main">
				<h3><?php echo $title ?></h3>
				<?php echo $content ?>
			</div>
			<div class="two-col-ti--sidebar">
				<img src="<?php echo $side_main['sizes']['large']; ?>"/>
				<img style="margin-top:10px;" src="<?php echo $side_second['sizes']['large']; ?>"/>
			</div>
		</div>
	</div>
</div>


</div>
<?php get_footer();
