<?php
/**
 * Staff Hero
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_url  = get_the_post_thumbnail_url( null, '800x' );
$post_title = get_the_title();

$page_title = get_field( 'page_title' );

$titles   = get_field( 'titles' );
$position = ! empty( $titles ) ? array_shift( $titles )['title'] : '';
if ( empty( $position ) ) {
	$position = get_field( 'positions' );
	$position = ! empty( $position ) ? array_shift( $position )['position'] : '';
}


$emails  = get_field( 'emails' );
$contact = ! empty( $emails ) ? array_shift( $emails )['email'] : '';

?>
<div class="section banner">

	<div class="banner-inner type-4">

		<div class="banner-wrapper">

			<div class="decor-banner-1" data-rellax-speed="-1" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/banner-decor-1.svg');"></div>

			<div class="container">
				<div class="row">
					<?php if ( ! empty( $image_url ) ) { ?>
					<div class="col-lg-5">
						<div class="banner-img">
							<div class="bg rellax" data-rellax-speed="-1"
								style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></div>
						</div>
					</div>
					<?php } ?>

					<div class="col-lg-7">
						<div class="banner-content">

							<div class="spacer-24"></div>

							<?php NYBC_Helpers::breadcrumbs(); ?>

							<div class="spacer-48 spacer-xs-32"></div>
							<?php if ( ! empty( $page_title ) ) { ?>
								<div class="h5 title fw-800"><?php echo esc_html( $page_title ); ?></div>
								<div class="spacer-24"></div>
							<?php } ?>

							<h1 class="h2 title fw-800"><?php echo esc_html( $post_title ); ?></h1>

							<?php if ( ! empty( $position ) ) { ?>
								<div class="spacer-16 spacer-xs-8"></div>
								<div class="h4 title fw-800 text-20"><?php echo esc_html( $position ); ?></div>
							<?php } ?>

							<?php if ( ! empty( $contact ) ) { ?>
								<div class="spacer-24"></div>
								<a href="mailto:<?php echo esc_attr( $contact ); ?>" class="btn btn-primary"><?php esc_html_e( 'Contact', 'nybc' ); ?></a>
							<?php } ?>
							<div class="spacer-24 spacer-xs-48"></div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


