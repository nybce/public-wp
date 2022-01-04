<?php
/**
 * Staff Lab Personnel
 *
 * @package NYBC
 */

$display_count = 2;
$lab_personnel = get_field( 'lab_personnel' );
if ( empty( $lab_personnel ) ) {
	return;
}

$block_title = $lab_personnel['title'];
$description = $lab_personnel['description'];
$staffs      = $lab_personnel['staff'];
if ( empty( $staffs ) ) {
	$staffs = array();
}
?>
<div class="section staff">
	<div class="decor-staff" data-rellax-speed="-1" style="background-image: url('<?php echo esc_url( NYBC_IMG_URI ); ?>');"></div>

	<div class="container container-lg">
		<div class="row justify-content-center">

			<div class="col-lg-8">
				<div class="staff-card-inner">

					<div class="text-xl">
						<h2 style="color: #0C2D83;"><?php echo esc_html( $block_title ); ?></h2>
						<?php if ( ! empty( $description ) ) { ?>
							<div class="spacer-24 spacer-xs-16"></div>
							<p><?php echo wp_kses_post( $description ); ?></p>
						<?php } ?>
					</div>

					<div class="spacer-40 spacer-xs-48"></div>

					<div class="staff-card-wrapper">
						<?php
						foreach ( $staffs as $i => $staff ) {
							$image_url  = get_the_post_thumbnail_url( $staff, '519x292' );
							$post_title = get_the_title( $staff );

							$image = array(
								'url' => $image_url,
								'alt' => $post_title,
							);

							$titles   = get_field( 'titles', $staff );
							$position = ! empty( $titles ) ? array_shift( $titles )['title'] : '';

							if ( empty( $position ) ) {
								$position = get_field( 'positions', $staff );
								$position = ! empty( $position ) ? array_shift( $position )['position'] : '';
							}

							$areas = get_the_terms( $staff, 'area_of_research' );

							if ( ! empty( $areas ) ) {
								$areas = array_map(
									function ( $a ) {
										return $a->name;
									},
									$areas
								);
								$areas = implode( ' | ', $areas );

							}
							?>
						<a href="#" class="staff-card" style="<?php echo esc_attr( $i >= $display_count ? 'display: none' : '' ); ?>">
							<div class="staff-card-img">

								<?php NYBC_Helpers::picture( $image ); ?>

							</div>

							<div class="staff-card-info">
								<div class="h5 title fw-800"><?php echo esc_html( $post_title ); ?></div>

								<?php if ( ! empty( $position ) ) { ?>
									<div class="spacer-4"></div>
									<div class="text-lg fw-500"><?php echo esc_html( $position ); ?></div>
								<?php } ?>



								<?php if ( ! empty( $areas ) ) { ?>
									<div class="spacer-16"></div>

									<div class="divider"></div>

									<div class="spacer-16"></div>

									<div class="text text-20 fw-700"><?php esc_html_e( 'Area of Research', 'nybc' ); ?></div>

									<div class="spacer-8"></div>

									<div class="text-lg text-10 fw-500"><?php echo esc_html( $areas ); ?></div>
								<?php } ?>

							</div>
						</a>
						<?php } ?>

						<?php if ( count( $staffs ) > $display_count ) { ?>
							<div class="spacer-24"></div>
							<button class="btn btn-small btn-primary load-more"><?php esc_html_e( 'Load More', 'nybc' ); ?></button>
							<script>
								jQuery(function ($) {
									$('.staff-card-wrapper .load-more').on('click', function () {
										$('.staff-card-wrapper').find('.staff-card:hidden').slice(0, <?php echo esc_html( $display_count ); ?>).show(200);
										if(!$('.staff-card-wrapper').find('.staff-card:hidden').length) $(this).hide();
									});
								});
							</script>
						<?php } ?>
					</div>

					<div class="spacer-48 spacer-xs-64"></div>
				</div>
			</div>
		</div>
	</div>

</div>
