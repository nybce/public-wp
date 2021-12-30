<?php
/**
 * Staff Content
 *
 * @package NYBC
 */

$titles    = get_field( 'titles' );
$emails    = get_field( 'emails' );
$member    = get_field( 'member' );
$phones    = get_field( 'phones' );
$positions = get_field( 'positions' );
?>

<div class="section staff">

	<div class="container container-lg">
		<div class="row justify-content-center">

			<div class="col-lg-8">
				<div class="spacer-64 spacer-xs-48"></div>

				<div class="personal-info-wrapper">
					<?php if ( ! empty( $titles ) ) { ?>
					<div class="personal-info">
						<div class="h6 title fw-900"><?php esc_html_e( 'Title', 'nybc' ); ?></div>

						<div class="spacer-8"></div>

						<?php foreach ( $titles as $item ) { ?>
							<div class="text-xl"><?php echo esc_html( $item['title'] ); ?></div>
							<div class="spacer-24"></div>
						<?php } ?>

					</div>
					<?php } ?>

					<?php if ( ! empty( $positions ) ) { ?>
					<div class="personal-info">
						<div class="h6 title fw-900"><?php esc_html_e( 'Position', 'nybc' ); ?></div>

						<div class="spacer-8"></div>

						<?php foreach ( $positions as $item ) { ?>
							<div class="text-xl"><?php echo esc_html( $item['position'] ); ?></div>
							<div class="spacer-24"></div>
						<?php } ?>

					</div>
					<?php } ?>

					<?php if ( ! empty( $member ) ) { ?>
					<div class="personal-info">
						<div class="h6 title fw-900"><?php esc_html_e( 'Member', 'nybc' ); ?></div>

						<div class="spacer-8"></div>

						<?php foreach ( $member as $item ) { ?>
							<div class="text-xl"><?php echo esc_html( $item['member'] ); ?></div>
							<div class="spacer-24"></div>
						<?php } ?>

					</div>
					<?php } ?>

					<?php if ( ! empty( $emails ) || ! empty( $phones ) ) { ?>
					<div class="personal-info">
						<div class="h6 title fw-900"><?php esc_html_e( 'Contact', 'nybc' ); ?></div>

						<div class="spacer-8"></div>

						<?php
						if ( ! empty( $emails ) ) {
							foreach ( $emails as $item ) {
								?>
						<div class="contact-link">
							<span><?php esc_html_e( 'E', 'nybc' ); ?></span>
							<a href="mailto:<?php echo esc_attr( $item['email'] ); ?>"><?php echo esc_html( $item['email'] ); ?></a>
						</div>
						<div class="spacer-8"></div>
								<?php
							}
						}
						?>
						<?php
						if ( ! empty( $phones ) ) {
							foreach ( $phones as $item ) {
								?>
								<div class="contact-link">
									<span><?php esc_html_e( 'P', 'nybc' ); ?></span>
									<a href="tel:<?php echo esc_attr( NYBC_Helpers::tel( $item['phone'] ) ); ?>"><?php echo esc_html( $item['phone'] ); ?></a>
								</div>
								<div class="spacer-8"></div>
								<?php
							}
						}
						?>
						<div class="spacer-8"></div>
					</div>
					<?php } ?>

				</div>

				<div class="spacer-64 spacer-xs-48"></div>

				<div class="text-lg">
					<?php the_content(); ?>
				</div>

			</div>
		</div>
	</div>

	<div class="spacer-96"></div>

</div>

<?php get_template_part( 'template-parts/pages/staff/lab-personnel' ); ?>

<div class="spacer-64"></div>
