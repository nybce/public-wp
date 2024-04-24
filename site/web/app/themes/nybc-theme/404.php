<?php
/**
 * The template for displaying 404 pages (not found)
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
		get_template_part( 'template-parts/pages/404-hero' );
		get_template_part( 'template-parts/pages/recent-news' );
		?>
	</main>
<?php
get_footer();
