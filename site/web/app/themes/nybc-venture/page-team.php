<?php

/*
  Template Name: Team
  */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

get_header();

$hero_image = get_field('hero_image');
$title = get_field('title');
$intro_text = get_field('introduction_text');
?>
<div class="page-wrapper team-page">

<div class="reduced-wrapper">
  <div class="container">
  	<div class="hero-image">
  		<img src="<?php echo $hero_image['url'] ?>"/>
  	</div>
	</div>
</div>

<div class="major-reduced-wrapper">
	<div class="container">
		<div class="nybcv-block">
			<div class="nybcv-block--intro">
				<h3><?php echo get_field('title')?></h3>
				<?php echo get_field('introduction_text') ?>
			</div>
			<div class="team-grid">

<?php

$args = array(
		'post_status' => 'publish',
    'post_type' => 'team-member',
    'posts_per_page' => -1,
    'meta_key' => 'order_in_sorting',
    'orderby' => 'meta_value_num',
    'order' => 'DESC'
  );

// The Query
$team_query = new WP_Query( $args );

// The Loop
if ( $team_query->have_posts() ) {
	while ( $team_query->have_posts() ) {
		$team_query->the_post();

		$thumbnail_image = get_field('thumbnail_image');
		$full_name = get_field('full_name');
		$job_title = get_field('job_title');
		$biography = get_field('biography');
?>

<div class="team-grid--profile">
	<div class="team-grid--profile--image">
		<a href="<?php echo get_permalink() ?>">
			<img src="<?php echo $thumbnail_image['sizes']['large']; ?>"/>
		</a>
	</div>
	<div class="team-grid--profile--text">
		<h5><a href="<?php echo get_permalink() ?>"><?php echo $full_name ?></a></h5>
		<h6><?php echo $job_title ?></h6>
	</div>
</div>

<?php
	}

} else {
	// no posts found
}
/* Restore original Post Data */
wp_reset_postdata();
?>

			</div>
		</div>
	</div>
</div>


</div>
<?php get_footer();
