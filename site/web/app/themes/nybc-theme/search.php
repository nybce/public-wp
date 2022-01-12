<?php
/**
 * The template for displaying search results pages
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
	<main>

		<?php
		get_template_part( 'template-parts/pages/search/hero' );
		get_template_part( 'template-parts/pages/search/results' );
		get_template_part( 'template-parts/pages/recent-news' );
		?>
	</main>

<?php
get_footer();
