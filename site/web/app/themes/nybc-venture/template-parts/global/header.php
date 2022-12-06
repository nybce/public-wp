<?php
$path = $_SERVER['REQUEST_URI'];
?>

<header>
    <div class="header-inner">
        <div class="container">
            <div class="desktop-menu <?php if(is_front_page()){ echo 'no-border'; }?>">
                <a href="<?php echo HOME_URI; ?>" class="logo">NYBC VENTURES</a>

                <?php

$menu_args = array(
    'theme_location' => 'main-menu',
    'menu' => '',
    'container' => 'div',
    'container_class' => 'top-nav-ctn',
    'container_id' => '',
    'menu_class' => 'top-nav',
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
            <div class="mobile-menu">
                <a href="<?php echo HOME_URI; ?>" class="logo">NYBC VENTURES</a>
                
                <div class="menu-btn">
                    <span class="menu-btn--line"></span>
                    <span class="menu-btn--line"></span>
                    <span class="menu-btn--line"></span>
                    <span class="menu-btn--line"></span>
                    <span class="menu-btn--close"></span>
                </div>
            </div>
            <div class="toggle-block">
                <?php

$menu_args = array(
    'theme_location' => 'main-menu',
    'menu' => '',
    'container' => 'div',
    'container_class' => 'mobile-nav-ctn',
    'container_id' => '',
    'menu_class' => 'mobile-nav',
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
        </div>
    </div>
</header>