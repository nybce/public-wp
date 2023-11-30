<footer>
    <div class="container">
        <div class="footer-bar">
            <div class="footer-menu">
                <div class="footer-menu--left">
                    <span class="copyright">
&copy; <?php echo esc_html( date_i18n( __( 'Y', 'nybcv' ) ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
</span>


<?php
$menu_args = array(
    'theme_location' => 'footer-menu',
    'menu' => '',
    'container' => 'div',
    'container_class' => 'footer-nav-ctn',
    'container_id' => '',
    'menu_class' => 'footer-nav',
    'menu_id' => '',
    'echo' => true,
    'fallback_cb' => 'wp_page_menu',
    'before' => '',
    'after' => '',
    'link_before' => '',
    'link_after' => '',
    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
    'depth' => 0,
    'walker' => '',
);
wp_nav_menu( $menu_args);
?>
</div>
<div class="footer-menu--right">
    <a href="https://twitter.com/VenturesNYBC" target="_blank" aria-label="twitter"><svg version="1.1" id="Layer_1" xmlns=" http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve" height="19" width="19">                           
                                   <path class="st0" d="M14.2,10.2l8.7-10.1h-2.1l-7.6,8.8l-6-8.8h-7l9.1,13.3L0.3,23.9h2.1l8-9.3l6.4,9.3h7L14.2,10.2L14.2,10.2z
                                       M11.4,13.5l-0.9-1.3L3.1,1.6h3.2l5.9,8.5l0.9,1.3l7.7,11h-3.2L11.4,13.5L11.4,13.5z" fill="black"></path>
                                   </svg></a>
    <a href="https://www.linkedin.com/company/nybc-ventures/about/" target="_blank" aria-label="linkedin"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
</div>
            </div>
        </div>
    </div>
</footer>