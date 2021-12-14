<?php
/**
 * Header part
 *
 * @package NYBC
 */

$one_line_logo = get_field( 'one_line_logo', 'options' );

?>
<div class="header-inner">

	<div class="header-top">
		<div class="top-menu">
			<?php
			if ( has_nav_menu( 'main_nav' ) ) {
				$main_nav = wp_nav_menu(
					array(
						'container'      => false,
						'menu_class'     => 'nav-list',
						'theme_location' => 'main_nav',
						'depth'          => 1,
						'echo'           => true,
					)
				);
			}
			?>
			<div class="btn-wrapper">
				<form class="btn-search">
					<input class="input" type="search" placeholder="<?php esc_html_e( 'Search', 'nybc' ); ?>">
					<button>
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M7.5 13.334a5.8 5.8 0 003.489-1.167l5.677 5.679 1.179-1.179-5.678-5.678A5.8 5.8 0 0013.333 7.5 5.833 5.833 0 107.5 13.334zm0-10A4.171 4.171 0 0111.666 7.5 4.171 4.171 0 017.5 11.667 4.171 4.171 0 013.333 7.5 4.171 4.171 0 017.5 3.334z"
								fill="#FC1921" />
						</svg>
					</button>
				</form>
				<a href="#" class="btn btn-small btn-primary"><?php esc_html_e( 'Login', 'nybc' ); ?></a>
				<a href="#" class="btn btn-small btn-primary"><?php esc_html_e( 'Support Us', 'nybc' ); ?></a>
			</div>
		</div>
	</div>

	<div class="header-bottom">
		<div class="mobile-menu">
			<a href="<?php echo esc_html( NYBC_HOME_URI ); ?>" class="logo">
				<?php if ( ! empty( $one_line_logo ) ) { ?>
					<img src="<?php echo esc_html( $one_line_logo['url'] ); ?>" alt="<?php echo esc_html( $one_line_logo['alt'] ); ?>">
				<?php } ?>
			</a>

			<div class="toggle-block">
				<nav class="nav-wrapper">
					<?php get_template_part( 'template-parts/header/page-menu' ); ?>
				</nav>

				<div class="top-menu mobile">
					<?php
					if ( has_nav_menu( 'main_nav' ) ) {
						$main_nav = wp_nav_menu(
							array(
								'container'      => false,
								'menu_class'     => 'nav-list',
								'theme_location' => 'main_nav',
								'depth'          => 1,
								'echo'           => true,
							)
						);
					}
					?>
					<div class="btn-wrapper">
						<form class="btn-search">
							<input class="input" type="search" placeholder="<?php esc_html_e( 'Search', 'nybc' ); ?>">
							<button>
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path
										d="M7.5 13.334a5.8 5.8 0 003.489-1.167l5.677 5.679 1.179-1.179-5.678-5.678A5.8 5.8 0 0013.333 7.5 5.833 5.833 0 107.5 13.334zm0-10A4.171 4.171 0 0111.666 7.5 4.171 4.171 0 017.5 11.667 4.171 4.171 0 013.333 7.5 4.171 4.171 0 017.5 3.334z"
										fill="#FC1921" />
								</svg>
							</button>
						</form>
						<a href="#" class="btn btn-small btn-primary"><?php esc_html_e( 'Login', 'nybc' ); ?></a>
						<a href="#" class="btn btn-small btn-primary"><?php esc_html_e( 'Support Us', 'nybc' ); ?></a>
					</div>
				</div>
			</div>

			<div class="mobile-button-wrapper">
				<div class="mobile-button"><span></span></div>
			</div>

		</div>
	</div>
</div>
