<?php

/*
  Template Name: News
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
					<h3><?php echo get_field('title') ?></h3>
					<?php echo get_field('introduction_text') ?>
				</div>

				<div class="portfolio-grid">
					<?php
					if (have_posts()) :
						while (have_posts()) : the_post();


					?>
					<?php echo '<pre>';
							print_r($wp_query->post);
							echo '</pre>'; ?>

					<article>
						<h2><?php the_title(); ?></h2>
						<div class="post-content">
							<?php the_content(); ?>
						</div>
						<?php if (has_post_thumbnail()) : ?>
						<div class="post-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>
					</article>
					<?php
						endwhile;
					else :
						echo 'No News Articles found.';
					endif;
					?>
				</div>
			</div>
		</div>
	</div>


</div>
<?php get_footer();