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
							<!-- Citation -->
							<?php
							$citation = get_post_meta(get_the_ID(), 'citation', true);
							if (!empty($citation)) {
								echo '<p class="post-citation">';
								echo esc_html($citation);
								echo '</p>';
							}
							?>
							<!-- Title -->
							<h1 class="post-title"><?php the_title(); ?></h1>
							<!-- Date -->
							<div class="post-date">
								<?php echo strtoupper(get_the_date()); ?>
							</div>
							<!-- Subtitle -->
							<?php
							$subtitle = get_post_meta(get_the_ID(), 'subtitle', true);
							if (!empty($subtitle)) {
								echo '<p class="post-subtitle">';
								echo esc_html($subtitle);
								echo '</p>';
							}
							?>
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

<style>
	/* CSS for Single Post Content Layout */
	.single-post-content {}

	.post-title {
		font-size: 50px;
	}

	.post-citation,
	.post-title {
		text-transform: uppercase;
	}

	.post-citation,
	.post-title,
	.post-date,
	.post-subtitle {
		margin-bottom: 24px;
		text-align: center;
	}

	.post-subtitle {
		font-size: 24px;
	}

	.wp-block-image figcaption {
		font-size: 19px;
		line-height: 1.1;
	}

	.post-content .wp-block-columns {
		gap: 30px;
	}

	@media only screen and (max-width: 703px) {
		.post-content .wp-block-columns .wp-block-column:last-child {
			order: 1;
		}

		.post-content .wp-block-columns .wp-block-column:first-child {
			order: 2;
		}

		.post-title {
			font-size: 36px;
		}
	}
</style>

<?php get_footer(); ?>