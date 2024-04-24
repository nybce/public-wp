<?php
/**
 * News Hero
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_type	= get_post_type();
$image_url  = get_the_post_thumbnail_url( null, '800x' );
$post_title = get_the_title();
$date       = get_the_time( 'F j, Y' );
$tags       = get_the_tags();
?>
<div class="section banner mb-48">

	<div class="banner-inner type-4">

		<div class="banner-wrapper">

			<div class="decor-banner-1" data-rellax-speed="-1"
				style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/banner-decor-1.svg');"></div>

			<div class="container">

				<div class="row">
					<?php 
					// Tempfix, do not show featured image for news post in all cases
					// if ( ! empty( $image_url ) ) {
					if(false) { ?>
					<div class="col-lg-5">
						<div class="banner-img size-2">
							<div class="bg rellax" data-rellax-speed="-1"
								style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></div>
						</div>
					</div>
					<?php } ?>

					<!--<div class="col-lg-7">-->
					<div class="col-lg-12">
						<div class="banner-content">

							<div class="spacer-24"></div>

							<?php NYBC_Helpers::breadcrumbs(); ?>

							<div class="spacer-48 spacer-xs-32"></div>

							<?php if($post_type != 'story'): ?>
							<div class="h5 title fw-800"><?php echo esc_html( $date ); ?></div>
							<?php endif; ?>

							<div class="spacer-24"></div>

							<h1 class="h2 title fw-800"><?php echo esc_html( $post_title ); ?></h1>

							<div class="spacer-24"></div>
							<?php if ( ! empty( $tags ) ) { ?>
							<div class="tags-wrapper">
								<?php foreach ( $tags as $tg ) { ?>
									<a href="<?php echo esc_url( get_term_link( $tg ) ); ?>" class="tag"><?php echo esc_html( $tg->name ); ?></a>
								<?php } ?>
							</div>
							<?php } ?>

							<div class="spacer-24 spacer-xs-48"></div>
						</div>
					</div>

				</div>

			</div>

		</div>
	</div>
</div>
