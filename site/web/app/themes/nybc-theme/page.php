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

<main id="content">

<?php the_content(); ?>

</main>
<?php
get_footer();
