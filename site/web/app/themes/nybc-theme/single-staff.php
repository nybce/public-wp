<?php
/**
 * Staff post type template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package _s
 */

get_header(); ?>
	<main>
		<?php
		while ( have_posts() ) {
			the_post();

			get_template_part( 'template-parts/pages/staff/hero' );
			get_template_part( 'template-parts/pages/staff/content' );
			get_template_part( 'template-parts/pages/recent-news' );
		}
		?>
	</main>
<?php
get_footer();
