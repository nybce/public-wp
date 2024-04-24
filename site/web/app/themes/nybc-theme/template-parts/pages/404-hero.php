<?php
/**
 * Staff Recent News
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="section banner">

	<div class="banner-inner type-4">

		<div class="banner-wrapper">

			<div class="decor-banner-2" data-rellax-speed="-1" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>/banner-decor-2.svg');"></div>

			<div class="container">

				<div class="row">

					<div class="col-lg-7">
						<div class="banner-content">

							<div class="spacer-24"></div>

							<div class="spacer-48 spacer-xs-32"></div>

							<div class="h5 title fw-800"><?php esc_html_e( '404 Not Found', 'nybc' ); ?></div>

							<div class="spacer-24"></div>

							<h1 class="h2 title fw-800"><?php esc_html_e( 'The page you are looking for cannot be found.', 'nybc' ); ?></h1>

							<div class="spacer-24"></div>

							<a href="<?php echo esc_url( NYBC_HOME_URI ); ?>" class="btn btn-primary"><?php esc_html_e( 'Back to Home', 'nybc' ); ?></a>

							<div class="spacer-24 spacer-xs-48"></div>
						</div>
					</div>

				</div>

			</div>

		</div>
	</div>
</div>
