<?php
/**
 * Staff post type template file
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

			get_template_part( 'template-parts/pages/staff/hero' );
			get_template_part( 'template-parts/pages/staff/content' );
			get_template_part( 'template-parts/pages/recent-news' );
		}
		?>
	</main>
<?php
get_footer();
