<?php
/*
 * Id: $ID$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 
 * @param string $name preference group and name
 * @param mixed $default
 */
function cfg($name, $default = '') {
    static $cfgs = false;
    if (! $cfgs) {
        imports ( 'admin/models/CorePreferenceTable.php' );
        $cpt = new CorePreferenceTable ();
        $data = $cpt->query ();
        $cfgs = $data->walk ( array ($cpt, 'map' ) );
    }
    if (strpos ( $name, '@', 1 ) === false) {
        $name .= '@core';
    }
    if (isset ( $cfgs [$name] )) {
        $default = $cfgs [$name];
    }
    return $default;
}
function show_page_tip($message, $type = '') {
    if ($message) {
        $_SESSION ['_ksg_page_tip_info'] = $message;
        $_SESSION ['_ksg_page_tip_info_cls'] = 'alert-' . $type;
    }
}
/**
 *
 * @param Smarty $smarty        	
 */
function set_site_global_vars($smarty) {
    $settings = KissGoSetting::getSetting ();
    $smarty->assign ( 'ksg_site_url', BASE_URL );
    $smarty->assign ( 'ksg_passport', Passport::getPassport () );
    $smarty->assign ( 'ksg_version', KISSGO_VERSION );
    $smarty->assign ( 'ksg_build', KISSGO_BUILD );
    $smarty->assign ( 'ksg_uri', Request::getUri () );
    $smarty->assign ( 'ksg_url', Request::getVirtualPageUrl () );
    $smarty->assign ( '_ksg_page_tip_info', sess_del ( '_ksg_page_tip_info', false ) );
    $smarty->assign ( '_ksg_page_tip_info_cls', sess_del ( '_ksg_page_tip_info_cls', '' ) );
    return $smarty;
}
bind ( 'init_smarty_engine', 'set_site_global_vars' );
/**
 * 设置管理界面全局数据
 *
 * @param Smarty $smarty        	
 */
function set_admin_global_vars($smarty) {
    $smarty->assign ( 'ksg_top_navigation_menu', apply_filter ( 'get_top_navigation_menu', new NavigationMenuManager () ) );
    $smarty->assign ( 'ksg_foot_toolbar_btns', apply_filter ( 'get_foot_toolbar_buttons', new NavigationFootToolbar () ) );
    return $smarty;
}
bind ( 'init_view_smarty_engine', 'set_admin_global_vars' );
/**
 * 设置导航菜单
 *
 * @param NavigationMenuManager $mm        	
 * @return mixed
 */
function _hook_for_admincp_menu($mm) {
    // System
    $mm->addMenu2 ( 'system', __ ( 'System' ), 'icon-th-large' );
    $mm->addMenuItem ( 'system', 'menuitem-user-role', __ ( 'Users & Roles' ), '#', 'icon-user' );
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-users', __ ( 'Users Management' ), murl ( 'admin', 'users' ), 'icon-user' );
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-roles', __ ( 'Roles Management' ), murl ( 'admin', 'roles' ), 'icon-user' );
    $mm->addMenuItemDivider ( 'system' );
    $mm->addMenuItem ( 'system', 'menuitem-modules', __ ( 'Extensions' ), murl ( 'admin', 'extension' ), 'icon-briefcase' );
    $mm->addMenuItem ( 'system', 'menuitem-options', __ ( 'Preferences' ), murl ( 'admin', 'preference' ), 'icon-adjust' );
    /*// Web Site
    $mm->addMenu2 ( 'menu-website', __ ( 'Website' ), 'icon-globe' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-pages', __ ( 'Pages' ), murl ( 'kissgo', 'pages' ), 'icon-file' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-comments', __ ( 'Comments' ), murl ( 'kissgo', 'comments' ), 'icon-comment' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-category', __ ( 'Categories' ), murl ( 'kissgo', 'categoris' ), 'icon-briefcase' );
    $mm->addMenuItemDivider ( 'menu-website' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-pagetypes', __ ( 'Page Types' ), murl ( 'kissgo', 'pagetypes' ), 'icon-list' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-themes', __ ( 'Themes & Templates' ), murl ( 'kissgo', 'themes' ), 'icon-list' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-menus', __ ( 'Menus' ), murl ( 'kissgo', 'menus' ), 'icon-list' );
    $mm->addMenuItemDivider ( 'menu-website' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-attachs', __ ( 'Attachments' ), murl ( 'kissgo', 'attachs' ), 'icon-picture' );
    // Components
    $mm->addMenu2 ( 'menu-components', __ ( 'Components' ), 'icon-cog' );
    $mm->addMenuItem ( 'menu-components', 'menuitem-codes', __ ( 'Code Fragments' ), murl ( 'kissgo', 'fragments' ), 'icon-list-alt' );
    $mm->addMenuItem ( 'menu-components', 'menuitem-links', __ ( 'Links' ), murl ( 'kissgo', 'links' ), 'icon-retweet' );
    $mm->addMenuItem ( 'menu-components', 'menuitem-tags', __ ( 'Tags' ), murl ( 'kissgo', 'tags' ), 'icon-tags' );
    $mm->addMenuItem ( 'menu-components', 'menuitem-props', __ ( 'Properties' ), murl ( 'kissgo', 'properties' ), 'icon-tag' );
    $mm->addMenuItem ( 'menu-components', 'menuitem-enums', __ ( 'Enums' ), '', 'icon-book' );
    $mm->addSubItem ( 'menu-components/menuitem-enums', 'subitems-enums-authors', __ ( 'Authors' ), murl ( 'kissgo', 'authors' ), 'icon-book' );
    $mm->addSubItem ( 'menu-components/menuitem-enums', 'subitems-enums-origins', __ ( 'Origins' ), murl ( 'kissgo', 'origins' ), 'icon-book' );
    $mm->addSubItem ( 'menu-components/menuitem-enums', 'subitems-enums-keywords', __ ( 'Keywords' ), murl ( 'kissgo', 'keywords' ), 'icon-book' );
    */
    return $mm;
}
bind ( 'get_top_navigation_menu', '_hook_for_admincp_menu' );
// add new menu items
function _hook_add_new_menu_items($items) {
    $items .= '<li><a href="#">new page</a></li>';
    return $items;
}
//bind ( 'add_new_menu_items', '_hook_add_new_menu_items' );
function _hook_for_add_passport_menu_items($items) {
    $items .= '<li><a href="' . murl ( 'passport' ) . '">' . __ ( 'Control Panel' ) . '</a></li>';
    return $items;
}
bind ( 'add_passport_menu_items', '_hook_for_add_passport_menu_items' );
/**
 * 设置底部按键
 *
 * @param NavigationFootToolbar $tb        	
 */
function _hook_for_foot_toolbar($tb) {
    return $tb;
}

bind ( 'get_foot_toolbar_buttons', '_hook_for_foot_toolbar' );
/**
 * 
 * 读取登录用户信息
 * @param Passport $passport
 */
function _kissgo_hook_for_get_user_passport($passport) {
    if ($passport->isLogin ()) {
        $uid = $passport ['uid'];
        $user = sess_get ( 'login_user_info_' . $uid, false );
        if (! $user) {
            imports ( 'admin/models/*' );
            $um = new CoreUserTable ();
            $user = $um->query ()->where ( array ('uid' => $uid ) );
            if (count ( $user )) {
                $_SESSION ['login_user_info_' . $uid] = $user [0];
            } else {
                $user = false;
            }
        }
        if ($user) {
            $passport ['email'] = $user ['email'];
        }
    }
    return $passport;
}

bind ( 'get_user_passport', '_kissgo_hook_for_get_user_passport' );
// end of __init__.php