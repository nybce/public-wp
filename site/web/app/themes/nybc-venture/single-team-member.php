<?php 

/*
Template Name: Team Member
*/

get_header(); 
?>

<div class="single-wrapper team-member-single">
<div class="med-reduced-wrapper">
  <div class="container">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 

$profile_image = get_field('profile_image');
$full_name = get_field('full_name');
$job_title = get_field('job_title');
$biography = get_field('biography');
$sort_order = get_field('order_in_sorting');
	?>
	<div class="team-member">
			<div class="team-member--spacer"></div>
			<div class="team-member--title">
				<h1><?php echo $full_name ?></h1>
				<h3><?php echo $job_title ?></h3>
			</div>
			<div class="team-member--profile-image">
				<img src="<?php echo $profile_image['url'] ?>"/>
			</div>
			<div class="team-member--biography">
				<?php echo $biography ?>
				<div class="team-member--navigation">
<?php
$prev_post = get_adjacent_post(false, '', true);
$next_post = get_adjacent_post(false, '', false);

if(!empty($prev_post)):
	?>
<a class="team-member--navigation--previous" href="<?php echo get_permalink($prev_post->ID) ?>" title="<?php echo $prev_post->post_title ?>">Previous</a>
	<?php
endif;


if(!empty($prev_post) && !empty($next_post)):
?>
<span class="team-member--navigation--separator">|</span>
<?php
endif;

if(!empty($next_post)):?>
<a class="team-member--navigation--next" href="<?php echo get_permalink($next_post->ID) ?>" title="<?php echo $next_post->post_title ?>">Next</a>
<?php endif; ?>
				</div>
			</div>
	</div>
<?php endwhile; endif; ?>
  </div>
</div>
</div>
<?php get_footer(); ?>