<?php
/*
Template Name: Single Post
*/

get_header(); ?>

<div class="page-wrapper single-post">
	<div class="nybcv-wrapper">
		<div class="container">
			<div class="nybcv-block">
				<!-- Start of Single Post Content -->
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<div class="single-post-content">
							<!-- Author -->
							<div class="author">
								<?php the_author(); ?>
							</div>
							<!-- Title -->
							<h1 class="post-title">
								<?php the_title(); ?>
							</h1>
							<!-- Date -->
							<div class="post-date">
								<?php echo strtoupper(get_the_date()); ?>
							</div>
							<!-- Content -->
							<div class="post-content">
								<?php the_content(); ?>
							</div>
						</div>
				<?php endwhile;
				endif; ?>
				<!-- End of Single Post Content -->
			</div>
		</div>
	</div>
</div>

<footer class="footer">
	<?php get_template_part('nav', 'below-single'); ?>
</footer>

<?php get_footer(); ?>