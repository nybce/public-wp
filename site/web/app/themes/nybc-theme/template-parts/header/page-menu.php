<?php
/**
 * Header nav menu
 *
 * @package NYBC
 */

$locations = get_nav_menu_locations();
if ( isset( $locations['page_menu'] ) ) {

	$nav               = wp_get_nav_menu_items( $locations['page_menu'] );
	$nav               = NYBC_Helpers::menu_tree( $nav );
	$queried_object_id = get_queried_object_id();
	?>
<ul class="nav-list">
	<?php
	foreach ( $nav as $item ) {
		$text              = get_field( 'text', $item->ID );
		$number_of_columns = get_field( 'number_of_columns', $item->ID );
		?>
	<li class="dropdown-item <?php echo esc_html( $queried_object_id === $item->object_id ? 'current-menu-item' : '' ); ?>">
		<a href="<?php echo esc_html( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
		<?php if ( ( isset( $item->children ) && ! empty( $item->children ) ) || ! empty( $text ) ) { ?>
		<div class="dropdown-btn"></div>
		<div class="dropdown">
			<div class="dropdown-close"><i></i><span><?php esc_html_e( 'Back', 'nybc' ); ?></span></div>

			<div class="container">
				<div class="row justify-content-between">

					<div class="col-xl-6 col-12 align-self-center">
						<div class="dropdown-info">
							<a href="<?php echo esc_html( $item->url ); ?>" class="btn-link btn-link-secondary right"><?php echo esc_html( $item->title ); ?><i></i></a>
							<?php if ( ! empty( $text ) ) { ?>
							<div class="text-xl"><?php echo esc_html( $text ); ?></div>
							<?php } ?>
						</div>
					</div>
					<?php
					if ( isset( $item->children ) && ! empty( $item->children ) ) {
						$count   = count( $item->children );
						$columns = array( $item->children );
						if ( 'Two' === $number_of_columns ) {
							$columns = array(
								array_slice( $item->children, 0, $count / 2 ),
								array_slice( $item->children, $count / 2 ),
							);
						}
						?>
					<div class="col-xl-6 col-12 align-self-center">
						<div class="dropdown-list">
							<?php foreach ( $columns as $column ) { ?>
							<ul class="dropdown-list-menu">
								<?php
								foreach ( $column as $item2 ) {
									$current = '';
									if ( $queried_object_id === $item2->object_id ) {
										$current = 'current-menu-item';
									}
									?>
									<li class="<?php echo esc_attr( $current ); ?>"><a  href="<?php echo esc_url( $item2->url ); ?>"><?php echo esc_html( $item2->title ); ?></a></li>
									<?php

								}
								?>
							</ul>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</li>
	<?php } ?>
</ul>
<?php } ?>
