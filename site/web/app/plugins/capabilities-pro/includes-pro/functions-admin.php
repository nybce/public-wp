<?php
/*
 * PublishPress Capabilities Pro
 *
 * Pro admin functions with broad scope, which are not contained within a class
 */

add_action('admin_menu', 'pp_capabilities_admin_menu_permission', 99);

require_once (dirname(__FILE__) . '/features/custom.php');
\PublishPress\Capabilities\EditorFeaturesCustom::instance();

require_once (dirname(__FILE__) . '/features/metaboxes.php');
\PublishPress\Capabilities\EditorFeaturesMetaboxes::instance();

//admin features css hide integration
require_once (dirname(__FILE__) . '/features/admin-features-css-hide.php');
\PublishPress\Capabilities\AdminFeaturesCssHide::instance();

//admin features block url integration
require_once (dirname(__FILE__) . '/features/admin-features-block-url.php');
\PublishPress\Capabilities\AdminFeaturesBlockUrl::instance();

function pp_capabilities_admin_menu_permission()
{
    global $menu, $submenu;
    global $admin_global_menu, $admin_global_submenu;
    
    $ppc_global_menu     = (array)get_option('ppc_admin_menus_menu');
    $ppc_global_submenu  = (array)get_option('ppc_admin_menus_submenu');

    //let add a fallback value just incase
    if(!empty($ppc_global_menu)){
        $admin_global_menu = $ppc_global_menu;
    }else{
        $admin_global_menu 	  = (array)$GLOBALS['menu'];
    }
    if(!empty($ppc_global_submenu)){
        $admin_global_submenu = $ppc_global_submenu;
    }else{
        $admin_global_submenu = (array)$GLOBALS['submenu'];
    }
    

    if (is_object($admin_global_submenu)) {
        $admin_global_submenu = get_object_vars($admin_global_submenu);
    }

    if (!isset($admin_global_menu) || empty($admin_global_menu)) {
        $admin_global_menu = $menu;
    }

    if (!isset($admin_global_submenu) || empty($admin_global_submenu)) {
        $admin_global_submenu = $submenu;
    }

    //return if not admin page
    if (!is_admin()) {
        return;
    }

    //return if it's ajax request
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    $remove_menu = true;
    //We need to exclude restriction on Admin Menu Restrictions and use css instead due to new ways of getting menus to support custom menus
    if (isset($_GET['page']) && $_GET['page'] === 'pp-capabilities-admin-menus') {
        $remove_menu = false;
    }

    $disabled_menu 		 	 = '';
    $disabled_child_menu 	 = '';
    $user_roles			 	 = wp_get_current_user()->roles;

    // Support plugin integrations by allowing additional role-based limitations to be applied to user based on external criteria
    $user_roles = apply_filters('pp_capabilities_admin_menu_apply_role_restrictions', $user_roles, compact('menu', 'submenu'));

    $admin_menu_option = !empty(get_option('capsman_admin_menus')) 
    ? array_intersect_key((array)get_option('capsman_admin_menus'), array_fill_keys($user_roles, true)) : [];

    $admin_child_menu_option = !empty(get_option('capsman_admin_child_menus')) 
    ? array_intersect_key((array)get_option('capsman_admin_child_menus'), array_fill_keys($user_roles, true)) : [];

    /* 
     * PublishPress Permissions: Restrict Nav Menus for a Permission Group
     * (Integrate PublishPress Capabilities Pro functionality).
     *
     * Copy into functions.php, modifying $restriction_role and $permission_group_ids to match your usage.
     *
     * note: Restriction_role can be an extra role that you create just for these menu restrictions.
     *       Configure Capabilities > Nav Menus as desired for that role.
     */
    /*
    add_filter('pp_capabilities_admin_menu_apply_role_restrictions', 
        function($roles) {
            if (function_exists('presspermit')) {
                $permission_group_ids = [12, 14, 15];   // group IDs to restrict
                $restriction_role = 'subscriber';       // role that has restrictions defined by Capabilities > Nav Menus

                if (array_intersect(
                    array_keys(presspermit()->getUser()->groups['pp_group']), 
                    $permission_group_ids
                )) {
                    $roles []= $restriction_role;
                }
            }

            return $roles;
        }
    );
    */

    //extract disabled menu for roles user belong
    $disabled_menu_array = [];
    $disabled_child_menu_array = [];

    foreach ($admin_menu_option as $disabled_menus) {
        $disabled_menu_array = array_merge($disabled_menu_array, (array) $disabled_menus);
    }

    foreach ($admin_child_menu_option as $disabled_menus) {
        $disabled_child_menu_array = array_merge($disabled_child_menu_array, (array) $disabled_menus);
    }

    // Case of multiple user roles: If restriction priority is disabled, don't prevent access if any user role is unrestricted 
    if (count($user_roles) > 1 && !get_option('cme_admin_menus_restriction_priority', 1)) {
        foreach ($disabled_menu_array as $disabled_menu) {
            foreach ($user_roles as $role) {
                if (empty($admin_menu_option[$role]) || (!in_array($disabled_menu, $admin_menu_option[$role]))) {
                    $disabled_menu_array = array_diff($disabled_menu_array, (array) $disabled_menu);
                    continue 2;
                }
            }
        }

        foreach ($disabled_child_menu_array as $disabled_menu) {
            foreach ($user_roles as $role) {
                if (empty($admin_child_menu_option[$role]) || (!in_array($disabled_menu, $admin_child_menu_option[$role]))) {
                    $disabled_child_menu_array = array_diff($disabled_child_menu_array, (array) $disabled_menu);
                    continue 2;
                }
            }
        }
    }

    // if users.php menu is disabled, also disable profile.php
    if (in_array('users.php', $disabled_menu_array)) {
        $disabled_menu_array []= 'profile.php';
    }

    // deal with discrepancy between users.php > profile.php submenu location stored by Administrator vs. profile.php > profile.php loaded by limited users
    if (in_array('users.php15', $disabled_child_menu_array)) {
        if (empty($admin_global_submenu['users.php']) || empty($admin_global_submenu['users.php'][15])) {
            $disabled_child_menu_array[]= 'profile.php5';
        }
    }

    /*
    if (in_array('export-personal-data.php', $disabled_child_menu_array)) {
        if (isset($submenu['tools.php'][25]) && isset($submenu['tools.php'][25][2]) && ('export-personal-data.php' == $submenu['tools.php'][25][2])) {
            unset($submenu['tools.php'][25]);
        }
    }

    if (in_array('erase-personal-data.php', $disabled_child_menu_array)) {
        if (isset($submenu['tools.php'][30]) && isset($submenu['tools.php'][30][2]) && ('erase-personal-data.php' == $submenu['tools.php'][30][2])) {
            unset($submenu['tools.php'][30]);
        }
    }

	//$submenu['tools.php'][30] = array( __( 'Erase Personal Data' ), 'erase_others_personal_data', 'erase-personal-data.php' );
    */
    global $removed_menu_items, $removed_submenu_items;
    $removed_menu_items      = [];
    $removed_submenu_items   = [];
    foreach ($admin_global_menu as $key => $item) {
        if (isset($item[2])) {
            $menu_slug = $item[2];

            //remove menu and prevent page access if set
            if (in_array($menu_slug, $disabled_menu_array)) {
                if($remove_menu){
                    remove_menu_page($menu_slug);
                }
                pp_capabilities_admin_menu_access($menu_slug);
                $removed_menu_items []= $menu_slug;
                unset($submenu[$menu_slug]);
            }

            $check_slugs = [$menu_slug];
            if ('users.php' == $menu_slug) {
                $check_slugs []= 'profile.php';
            }

            foreach($check_slugs as $menu_slug) {
                //remove menu and prevent page access if set
                if (isset($admin_global_submenu) && !empty($admin_global_submenu[$menu_slug])) {
                    foreach ($admin_global_submenu[$menu_slug] as $subindex => $subitem) {
                        $sub_menu_value = $menu_slug . $subindex;

                        if (in_array($sub_menu_value, $disabled_child_menu_array)) {
                            if($remove_menu){
                                remove_submenu_page($menu_slug, $subitem[2]);
                            }
                            pp_capabilities_admin_menu_access($subitem[2]);
                            $removed_submenu_items[$menu_slug] = $subitem[2];
                            unset($menu[$menu_slug]);
                        }
                    }
                }
            }
        }
    }

    /**
     * due to conflict with custom menu which makes this function run earlier, 
     * we need to provide an additional measure to remove custom menus just 
     * incase they're added late. This is only UI related as 
     * pp_capabilities_admin_menu_access() will always block access to the 
     * page irrespective of then they added the menu
     *
     * @since 2.3.1
     */
     add_action('admin_footer', 'pp_capabilities_admin_menu_script');
}

function pp_capabilities_admin_menu_script(){
    global $removed_menu_items, $removed_submenu_items;

    ?>
    <script type="text/javascript">
     (function ($) {
        $(document).ready(function ($) {
            <?php 
            foreach($removed_menu_items as $menu_slug) {
                echo "$(\"#adminmenu li a[href='" . esc_url_raw($menu_slug) . "']\").closest('li').remove(); ";
            }

            foreach($removed_submenu_items as $menu_slug => $submenu_url) {
                echo "$(\"#adminmenu li a[href='" . esc_url_raw($menu_slug) . "']\").closest('li').find(\"ul li a[href='" . esc_url_raw($submenu_url) . "']\").closest('li').remove(); ";
            }
            ?>
        })
    })(jQuery)
    </script> 
    <?php     
}

function pp_capabilities_admin_menu_access($slug)
{
    $url = (isset($_SERVER['REQUEST_URI'])) ? esc_url_raw($_SERVER['REQUEST_URI']) : '';
    $url = basename($url);
    $url = htmlspecialchars($url);

    if (!isset($url)) {
        return false;
    }

    $uri = wp_parse_url($url);

    if (!isset($uri['path'])) {
        return false;
    }

    if (!isset($uri['query']) && strpos($uri['path'], $slug) !== false) {
        add_action('load-' . $slug, 'pp_capabilities_admin_menu_access_denied');
        return true;
    }

    if ($slug === $url) {
        add_action('load-' . basename($uri['path']), 'pp_capabilities_admin_menu_access_denied');
        return true;
    }

    if ($url == "admin.php?page=$slug") {
        pp_capabilities_admin_menu_access_denied();
    }
}

function pp_capabilities_admin_menu_access_denied()
{
    $forbidden = esc_attr__('You do not have permission to access this page.', 'capabilities-pro');
    wp_die(esc_html($forbidden));
}

function ppc_process_admin_menu_title($title)
{
    //strip count content
    $title = preg_replace('#<span class="(.*?)count-(.*?)">(.*?)</span>#', '', $title);

    //strip screen reader content
    $title = preg_replace('#<span class="(.*?)screen-reader-text(.*?)">(.*?)</span>#', '', $title);

    //strip other html tags
    $title = strip_tags($title);

    return $title;
}
