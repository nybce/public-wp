<?php
/**
 * The main template file
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

	<main>
		<?php
		while ( have_posts() ) {
			the_post();

			get_template_part( 'template-parts/pages/news-detail-hero' );
			the_content();
		}
		?>
	</main>
<?php
get_footer();
