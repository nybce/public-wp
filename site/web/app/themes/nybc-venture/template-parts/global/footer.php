<footer>
    <div class="footer-bar">
        <div class="container">
            <div class="footer-menu">
                <div class="footer-menu--left">
&copy; <?php echo esc_html( date_i18n( __( 'Y', 'nybcv' ) ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>


<?php
$menu_args = array(
    'theme_location' => 'footer-menu',
    'menu' => '',
    'container' => 'div',
    'container_class' => '',
    'container_id' => '',
    'menu_class' => 'menu',
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
    <a href="#">twitter</a>
    <a href="#">linkedin</a>
</div>
            </div>
        </div>
    </div>
</footer>