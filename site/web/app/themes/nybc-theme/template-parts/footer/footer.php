<?php
/**
 * Footer part
 *
 * @package NYBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$two_line_logos = array(
	array(
		'img_url' => NYBC_IMG_URI . '/nyblood.svg',
		'link'    => 'https://www.nybloodcenter.org/',
		'label'   => 'nyblood center',
	),
	array(
		'img_url' => NYBC_IMG_URI . '/community.svg',
		'link'    => 'https://savealifenow.org/',
		'label'   => 'savealifenow.org',
	),
	array(
		'img_url' => NYBC_IMG_URI . '/connecticut.svg',
		'link'    => 'https://www.ctblood.org/',
		'label'   => 'connecticut blood center',
	),
	array(
		'img_url' => NYBC_IMG_URI . '/delmarva.svg',
		'link'    => 'https://www.delmarvablood.org/',
		'label'   => 'delmarva',
	),
	array(
		'img_url' => NYBC_IMG_URI . '/memorial.svg',
		'link'    => 'https://www.mbc.org/',
		'label'   => 'memorial blood center',
	),
	array(
		'img_url' => NYBC_IMG_URI . '/nebraska.svg',
		'link'    => 'https://www.ncbb.org/',
		'label'   => 'ncbb',
	),
	array(
		'img_url' => NYBC_IMG_URI . '/new_jersey.svg',
		'link'    => 'https://www.nybc.org/',
		'label'   => 'new jersey blood services',
	),
	array(
		'img_url' => NYBC_IMG_URI . '/rhode_island.svg',
		'link'    => 'https://www.ribc.org/',
		'label'   => 'rhode island blood center',
	),
);

$one_line_logo = get_field( 'one_line_logo_footer', 'options' );

$footer_title = get_field( 'footer_title', 'options' );
$subtitle     = get_field( 'footer_subtitle', 'options' );
$address      = get_field( 'footer_address', 'options' );

$column_1_links = get_field( 'footer_column_1_links', 'options' );
$column_2_links = get_field( 'footer_column_2_links', 'options' );

$facebook_link = get_field( 'facebook_link', 'options' );
$twitter_link  = get_field( 'twitter_link', 'options' );
$linkedin_link = get_field( 'linkedin_link', 'options' );
$youtube_link  = get_field( 'youtube_link', 'options' );
$instagram_link  = get_field( 'instagram_link', 'options' );

$copyright_text     = get_field( 'copyright_text', 'options' );
$bottom_menu_link_1 = get_field( 'bottom_menu_link_1', 'options' );
$bottom_menu_link_2 = get_field( 'bottom_menu_link_2', 'options' );

?>

<div class="footer-inner">
	<div class="footer-top">

		<div class="spacer-96 spacer-xs-120"></div>

		<div class="container">
			<div class="row justify-content-center footer-top-inner">

				<div class="col-xl-8">
					<div class="title-wrapper type-2">
						<div class="h5 title light text-center"><?php echo esc_html( $footer_title ); ?></div>
						<div class="spacer-16"></div>
						<div class="text text-30 text-center"><?php echo esc_html( $subtitle ); ?></div>
					</div>
				</div>

				<div class="spacer-48 spacer-xs-24"></div>

				<div class="col-xl-10">
					<div class="logo-wrapper">
						<?php
						foreach ( $two_line_logos as $logo ) {
							if ( ! empty( $logo ) ) {
								?>
						<a href="<?php echo esc_url( $logo['link'] ); ?>" target="_blank" class="logo-img" aria-label=<?php echo esc_url( $logo['label'] ); ?>>
							<img src="<?php echo esc_url( $logo['img_url'] ); ?>" alt="" loading="lazy">
						</a>
								<?php
							}
						}
						?>
					</div>
				</div>

				<div class="spacer-48 spacer-xs-24"></div>
			</div>

			<div class="row">
				<div class="spacer-64 spacer-xs-48"></div>

				<div class="col-xl-3 col-lg-4 text-center">
					<div class="logo">
						<?php if ( ! empty( $one_line_logo ) ) { ?>
							<img src="<?php echo esc_url( $one_line_logo['url'] ); ?>" alt="<?php echo esc_attr( $one_line_logo['alt'] ); ?>">
						<?php } ?>
					</div>

					<div class="spacer-16"></div>

					<div class="address">
						<div class="text-sm text-30">
							<?php echo wp_kses_post( $address ); ?>
						</div>
					</div>

					<div class="spacer-xs-24"></div>
				</div>

				<div class="col-xl-3 col-lg-4">
					<?php if ( ! empty( $column_1_links ) ) { ?>
					<ul class="footer-nav-list">
						<?php
						foreach ( $column_1_links as $lnk ) {
							if ( ! empty( $lnk['link'] ) ) {
								$lnk = $lnk['link'];
								?>
								<li><a target="<?php echo esc_attr( $lnk['target'] ); ?>" href="<?php echo esc_url( $lnk['url'] ); ?>"><?php echo esc_html( $lnk['title'] ); ?></a></li>
								<?php
							}
						}
						?>
					</ul>
					<?php } ?>
					<div class="spacer-xs-4"></div>
				</div>

				<div class="col-xl-3 col-lg-4">
					<?php if ( ! empty( $column_2_links ) ) { ?>
						<ul class="footer-nav-list">
							<?php
							foreach ( $column_2_links as $lnk ) {
								if ( ! empty( $lnk['link'] ) ) {
									$lnk = $lnk['link'];
									?>
									<li><a target="<?php echo esc_attr( $lnk['target'] ); ?>" href="<?php echo esc_url( $lnk['url'] ); ?>"><?php echo esc_html( $lnk['title'] ); ?></a></li>
									<?php
								}
							}
							?>
						</ul>
					<?php } ?>
					<div class="spacer-xs-24"></div>
				</div>

				<div class="col-xl-3 col-lg-4 text-center">
					<div class="social-wrapper">
						<div class="h6 title light"><?php esc_html_e( 'Follow Us for Updates', 'nybc' ); ?></div>

						<div class="spacer-24"></div>

						<ul class="social-list">
							<?php if ( ! empty( $twitter_link ) ) { ?>
							<li>
								<a href="<?php echo esc_url( $twitter_link ); ?>" target="_blank" aria-label="twitter">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M8.94 18.705C10.2069 18.713 11.4627 18.4693 12.6346 17.9882C13.8066 17.507 14.8714 16.798 15.7672 15.9022C16.663 15.0064 17.3721 13.9416 17.8532 12.7696C18.3343 11.5977 18.578 10.3418 18.57 9.07499C18.57 8.92499 18.57 8.78249 18.57 8.63249C19.2267 8.15136 19.7951 7.56012 20.25 6.88499C19.6316 7.15544 18.9773 7.3348 18.3075 7.41749C19.0177 6.99432 19.5506 6.32824 19.8075 5.54249C19.1456 5.93957 18.4199 6.21871 17.6625 6.36749C17.1524 5.82401 16.4775 5.46378 15.7421 5.34253C15.0067 5.22128 14.2518 5.34576 13.5943 5.69673C12.9368 6.04769 12.4132 6.60557 12.1047 7.28405C11.7962 7.96252 11.7198 8.72376 11.8875 9.44999C10.542 9.38395 9.22553 9.03525 8.02377 8.42662C6.822 7.81798 5.7619 6.96305 4.9125 5.91749C4.48419 6.66087 4.35437 7.53923 4.54932 8.37473C4.74427 9.21023 5.24942 9.94043 5.9625 10.4175C5.43646 10.3972 4.92259 10.2533 4.4625 9.99749V10.035C4.45783 10.8119 4.71954 11.5669 5.20403 12.1742C5.68851 12.7815 6.3665 13.2044 7.125 13.3725C6.63573 13.5041 6.12322 13.5246 5.625 13.4325C5.84459 14.095 6.26376 14.6734 6.82503 15.0883C7.38631 15.5032 8.06219 15.7344 8.76 15.75C7.56691 16.7104 6.08407 17.239 4.5525 17.25C4.28396 17.2422 4.01606 17.2197 3.75 17.1825C5.30022 18.1702 7.1019 18.6909 8.94 18.6825" fill="white" />
									</svg>
								</a>
							</li>
							<?php } ?>
							<?php if ( ! empty( $facebook_link ) ) { ?>
							<li>
								<a href="<?php echo esc_url( $facebook_link ); ?>" target="_blank" aria-label="facebook">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M20.0025 3H3.9975C3.73355 3.00196 3.48097 3.10769 3.29433 3.29433C3.10769 3.48097 3.00196 3.73355 3 3.9975V20.0025C3.00196 20.2664 3.10769 20.519 3.29433 20.7057C3.48097 20.8923 3.73355 20.998 3.9975 21H12.615V14.04H10.275V11.3175H12.615V9.315C12.615 6.99 14.0325 5.7225 16.1175 5.7225C16.815 5.7225 17.5125 5.7225 18.21 5.8275V8.25H16.7775C15.645 8.25 15.4275 8.79 15.4275 9.5775V11.31H18.1275L17.775 14.0325H15.4275V21H20.0025C20.2664 20.998 20.519 20.8923 20.7057 20.7057C20.8923 20.519 20.998 20.2664 21 20.0025V3.9975C20.998 3.73355 20.8923 3.48097 20.7057 3.29433C20.519 3.10769 20.2664 3.00196 20.0025 3Z" fill="white" />
									</svg>
								</a>
							</li>
							<?php } ?>
							<?php if ( ! empty( $linkedin_link ) ) { ?>
							<li>
								<a href="<?php echo esc_url( $linkedin_link ); ?>" target="_blank" aria-label="linkedin">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M19.6575 3.00002H4.3425C3.99354 2.99793 3.65764 3.13255 3.40671 3.37506C3.15579 3.61757 3.0098 3.9487 3 4.29752V19.65C3.00788 19.9995 3.15327 20.3317 3.40461 20.5747C3.65596 20.8176 3.99297 20.9516 4.3425 20.9475H19.6575C20.007 20.9516 20.344 20.8176 20.5954 20.5747C20.8467 20.3317 20.9921 19.9995 21 19.65V4.29752C20.9902 3.9487 20.8442 3.61757 20.5933 3.37506C20.3424 3.13255 20.0065 2.99793 19.6575 3.00002ZM8.3325 18.3075H5.6925V9.75002H8.3325V18.3075ZM7.0425 8.55752C6.63717 8.55764 6.24786 8.39923 5.95777 8.11615C5.66767 7.83306 5.49979 7.44774 5.49 7.04252C5.48266 6.83667 5.5178 6.63151 5.59322 6.43983C5.66864 6.24815 5.78273 6.07406 5.92839 5.92841C6.07404 5.78276 6.24813 5.66867 6.43981 5.59324C6.63149 5.51782 6.83665 5.48268 7.0425 5.49002C7.43463 5.51733 7.80181 5.69236 8.06993 5.9798C8.33805 6.26725 8.48716 6.64569 8.48716 7.03877C8.48716 7.43186 8.33805 7.8103 8.06993 8.09774C7.80181 8.38519 7.43463 8.56022 7.0425 8.58752V8.55752ZM18.36 18.255H15.75V14.07C15.75 13.0725 15.75 11.775 14.355 11.775C12.96 11.775 12.75 12.87 12.75 13.9725V18.21H10.08V9.75002H12.57V10.875H12.6225C12.8777 10.4318 13.2499 10.0672 13.6983 9.82122C14.1467 9.5752 14.6541 9.45717 15.165 9.48002C17.8575 9.48002 18.36 11.28 18.36 13.5675V18.255Z" fill="white" />
									</svg>
								</a>
							</li>
							<?php } ?>
							<?php if ( ! empty( $youtube_link ) ) { ?>
							<li>
								<a href="<?php echo esc_url( $youtube_link ); ?>" target="_blank" aria-label="youtube">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none"	 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M22.0575 6.94502C21.9379 6.50105 21.7039 6.09626 21.3788 5.77115C21.0537 5.44604 20.6489 5.21203 20.205 5.09252C18.57 4.65002 12 4.65002 12 4.65002C12 4.65002 5.42995 4.65002 3.79495 5.09252C3.35098 5.21203 2.94619 5.44604 2.62108 5.77115C2.29597 6.09626 2.06196 6.50105 1.94245 6.94502C1.63716 8.61247 1.48901 10.3049 1.49995 12C1.48901 13.6952 1.63716 15.3876 1.94245 17.055C2.06196 17.499 2.29597 17.9038 2.62108 18.2289C2.94619 18.554 3.35098 18.788 3.79495 18.9075C5.42995 19.35 12 19.35 12 19.35C12 19.35 18.57 19.35 20.205 18.9075C20.6489 18.788 21.0537 18.554 21.3788 18.2289C21.7039 17.9038 21.9379 17.499 22.0575 17.055C22.3627 15.3876 22.5109 13.6952 22.5 12C22.5109 10.3049 22.3627 8.61247 22.0575 6.94502ZM9.89995 15.15V8.85002L15.3525 12L9.89995 15.15Z" fill="white" />
									</svg>
								</a>
							</li>
							<?php } ?>
							<?php if ( ! empty( $instagram_link ) ) { ?>
							<li>
								<a href="<?php echo esc_url( $instagram_link ); ?>" target="_blank" aria-label="instagram">
<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="24px" fill="white"
	 viewBox="0 0 24 24" xml:space="preserve" aria-hidden="true">
<path class="st0" d="M12,2.16c3.2,0,3.58,0.01,4.85,0.07c3.25,0.15,4.77,1.69,4.92,4.92c0.06,1.27,0.07,1.65,0.07,4.85
	c0,3.2-0.01,3.58-0.07,4.85c-0.15,3.23-1.66,4.77-4.92,4.92c-1.27,0.06-1.64,0.07-4.85,0.07c-3.2,0-3.58-0.01-4.85-0.07
	c-3.26-0.15-4.77-1.7-4.92-4.92C2.17,15.58,2.16,15.21,2.16,12c0-3.2,0.01-3.58,0.07-4.85C2.38,3.92,3.9,2.38,7.15,2.23
	C8.42,2.18,8.8,2.16,12,2.16z M12,0C8.74,0,8.33,0.01,7.05,0.07C2.7,0.27,0.27,2.69,0.07,7.05C0.01,8.33,0,8.74,0,12
	s0.01,3.67,0.07,4.95c0.2,4.36,2.62,6.78,6.98,6.98C8.33,23.99,8.74,24,12,24s3.67-0.01,4.95-0.07c4.35-0.2,6.78-2.62,6.98-6.98
	C23.99,15.67,24,15.26,24,12s-0.01-3.67-0.07-4.95c-0.2-4.35-2.62-6.78-6.98-6.98C15.67,0.01,15.26,0,12,0z M12,5.84
	C8.6,5.84,5.84,8.6,5.84,12S8.6,18.16,12,18.16s6.16-2.76,6.16-6.16C18.16,8.6,15.4,5.84,12,5.84z M12,16c-2.21,0-4-1.79-4-4
	c0-2.21,1.79-4,4-4s4,1.79,4,4C16,14.21,14.21,16,12,16z M18.41,4.15c-0.8,0-1.44,0.64-1.44,1.44s0.65,1.44,1.44,1.44
	c0.8,0,1.44-0.64,1.44-1.44S19.2,4.15,18.41,4.15z"/>
</svg>

								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>

				<div class="spacer-64"></div>
			</div>
		</div>

	</div>

	<div class="footer-bottom">
		<div class="spacer-24 spacer-xs-40"></div>

		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="copyright">
						<div class="text-sm text-40"><?php echo esc_html( $copyright_text ); ?></div>
					</div>

					<div class="spacer-xs-16"></div>
				</div>
				<div class="col-lg-6">
					<ul class="footer-nav-list">
						<?php if ( ! empty( $bottom_menu_link_1 ) ) { ?>
							<li><a href="<?php echo esc_url( $bottom_menu_link_1['url'] ); ?>"><?php echo esc_html( $bottom_menu_link_1['title'] ); ?></a></li>
						<?php } ?>
						<?php if ( ! empty( $bottom_menu_link_1 ) ) { ?>
							<li><a href="<?php echo esc_url( $bottom_menu_link_2['url'] ); ?>"><?php echo esc_html( $bottom_menu_link_2['title'] ); ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>

		<div class="spacer-24 spacer-xs-48"></div>
	</div>
</div>
