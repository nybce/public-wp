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
    <a href="https://twitter.com/VenturesNYBC" target="_blank"><i class="fa-brands fa-twitter"></i></a>
    <a href="https://www.linkedin.com/company/nybc-ventures/about/" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
</div>
            </div>
        </div>
    </div>
</footer>