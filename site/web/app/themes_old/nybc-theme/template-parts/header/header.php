<?php
/**
 * Header part
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$one_line_logo         = get_field( 'one_line_logo', 'options' );
$top_right_menu_link_1 = get_field( 'top_right_menu_link_1', 'options' );
$top_right_menu_link_2 = get_field( 'top_right_menu_link_2', 'options' );
$simple_banner = get_field( 'simple_banner', 'options' );
$banner_enable = get_field( 'banner_enable', 'options' );
$banner_open= get_field( 'banner_tab', 'options' );
$banner_url = get_field( 'banner_link', 'options' );
$banner_anchor ='#';
if ( ! empty( $banner_url ) && ! empty( $banner_url['url'] ) ) {
	$banner_anchor = $banner_url['url'];
}

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
				<form class="btn-search" action="<?php echo esc_url( NYBC_HOME_URI ); ?>">
					<?php wp_nonce_field( 'search', 'nonce' ); ?>
					<input name="s" class="input" type="search" placeholder="<?php esc_html_e( 'Search', 'nybc' ); ?>">
					<button type="submit" aria-label="submit">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M7.5 13.334a5.8 5.8 0 003.489-1.167l5.677 5.679 1.179-1.179-5.678-5.678A5.8 5.8 0 0013.333 7.5 5.833 5.833 0 107.5 13.334zm0-10A4.171 4.171 0 0111.666 7.5 4.171 4.171 0 017.5 11.667 4.171 4.171 0 013.333 7.5 4.171 4.171 0 017.5 3.334z"
								fill="#FC1921" />
						</svg>
					</button>
				</form>
				<?php if ( ! empty( $top_right_menu_link_1 ) ) { ?>
					<a class="btn btn-small btn-primary" target="<?php echo esc_attr( $top_right_menu_link_1['target'] ); ?>" href="<?php echo esc_url( $top_right_menu_link_1['url'] ); ?>"><?php echo esc_html( $top_right_menu_link_1['title'] ); ?></a>
				<?php } ?>
				<?php if ( ! empty( $top_right_menu_link_2 ) ) { ?>
					<a class="btn btn-small btn-primary" target="<?php echo esc_attr( $top_right_menu_link_2['target'] ); ?>" href="<?php echo esc_url( $top_right_menu_link_2['url'] ); ?>"><?php echo esc_html( $top_right_menu_link_2['title'] ); ?></a>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="header-bottom">
		<div class="mobile-menu">
			<a href="<?php echo esc_html( NYBC_HOME_URI ); ?>" class="logo" aria-label="NY BLOOD CENTER">
				<?php if ( ! empty( $one_line_logo ) ) { ?>
					<img src="<?php echo esc_html( $one_line_logo['url'] ); ?>" alt="NY BLOOD CENTER<?php echo esc_html( $one_line_logo['alt'] ); ?>">
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
						<form class="btn-search" action="<?php echo esc_url( NYBC_HOME_URI ); ?>">
							<?php wp_nonce_field( 'search', 'mobile-nonce' ); ?>
							<input name="s" class="input" type="search" placeholder="<?php esc_html_e( 'Search', 'nybc' ); ?>">
							<button type="submit" aria-label="search">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path
										d="M7.5 13.334a5.8 5.8 0 003.489-1.167l5.677 5.679 1.179-1.179-5.678-5.678A5.8 5.8 0 0013.333 7.5 5.833 5.833 0 107.5 13.334zm0-10A4.171 4.171 0 0111.666 7.5 4.171 4.171 0 017.5 11.667 4.171 4.171 0 013.333 7.5 4.171 4.171 0 017.5 3.334z"
										fill="#FC1921" />
								</svg>
							</button>
						</form>
						<?php if ( ! empty( $top_right_menu_link_1 ) ) { ?>
							<a class="btn btn-small btn-primary" target="<?php echo esc_attr( $top_right_menu_link_1['target'] ); ?>" href="<?php echo esc_url( $top_right_menu_link_1['url'] ); ?>"><?php echo esc_html( $top_right_menu_link_1['title'] ); ?></a>
						<?php } ?>
						<?php if ( ! empty( $top_right_menu_link_2 ) ) { ?>
							<a class="btn btn-small btn-primary" target="<?php echo esc_attr( $top_right_menu_link_2['target'] ); ?>" href="<?php echo esc_url( $top_right_menu_link_2['url'] ); ?>"><?php echo esc_html( $top_right_menu_link_2['title'] ); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="mobile-button-wrapper">
				<div class="mobile-button"><span></span></div>
			</div>

		</div>
	</div>
	<style>
.simple-banner {
    background-color: red;
    color: white;
    text-align: center;
    font-weight: 500;
    padding: 10px 20px;
	font-size:2rem;
}
.full-w {
	width:100%;
}
</style>
<?php if ( ! empty( $simple_banner ) && $banner_enable ) { ?>
	<a class="full-w" href ="<?php echo esc_url( $banner_anchor ); ?>" <?php if ( $banner_open ) { echo ' target="_blank" rel="noopener noreferrer"'; } ?>>
	<div class="simple-banner"><?php echo wp_kses( $simple_banner , array('br' => array(), 'i' => array(), 'b' => array(), 'u' => array(), 'strong' => array())); ?></div></a>
<?php } ?>
</div>
