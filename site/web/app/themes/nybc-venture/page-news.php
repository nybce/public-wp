<?php
/*
Template Name: News Page
*/

get_header(); ?>

<div class="page-wrapper portfolio-page">
	<div class="nybcv-wrapper">
		<div class="container">
			<div class="nybcv-block">
				<div class="nybcv-block--intro">
					<h3>News</h3>
				</div>

				<!-- Start of News Grid -->
				<div class="news-grid">
					<?php
					$args = array(
						// 'post_type' => 'news', // Replace with your custom post type if necessary
						'posts_per_page' => -1, // Display all news articles
						'orderby' => 'date',
						'order' => 'DESC',
					);

					$news_query = new WP_Query($args);

					if ($news_query->have_posts()) :
						while ($news_query->have_posts()) : $news_query->the_post();
					?>
					<a href="<?php the_permalink(); ?>" class="news-item">
						<?php if (has_post_thumbnail()) : ?>
						<div class="news-thumbnail">
							<?php the_post_thumbnail(); ?>
							<div class="image-caption">
								<h2 class="news-title"><?php the_title(); ?></h2>
								<div class="news-date"><?php echo get_the_date(); ?></div>
							</div>
						</div>
						<?php endif; ?>
					</a>
					<?php
						endwhile;
						wp_reset_postdata(); // Restore global post data
					else :
						echo 'No news articles found.';
					endif;
					?>
				</div>
				<!-- End of News Grid -->
			</div>
		</div>
	</div>
</div>

<style>
/* CSS for the news grid layout */
.news-grid {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	/* 2 columns */
	grid-gap: 20px;
	/* Adjust the gap between grid items as needed */
}

.news-item {
	text-decoration: none;
	color: #333;
	transition: transform 0.2s;
	overflow: hidden;
}

.news-thumbnail {
	position: relative;
	overflow: hidden;
}

.news-thumbnail img {
	max-width: 100%;
	height: auto;
}

.image-caption {
	position: absolute;
	bottom: 7px;
	left: 0;
	right: 0;
	background: rgba(255, 255, 255, 0.4);
	backdrop-filter: blur(7px);
	/* Adjust the background color and opacity as needed */
	color: black;
	padding: 20px 10px;
	text-align: center;
	transition-duration: .5s;
}

.news-item:hover .image-caption {
	background: rgba(255, 255, 255, .9);
}

.news-title,
.news-date {
	margin: 0;
	font-size: 18px;
}

.news-title {
	font-size: 20px;
}


@media only screen and (max-width: 700px) {
	.news-grid {
		grid-template-columns: 1fr;
	}
}
</style>

<?php get_footer(); ?>