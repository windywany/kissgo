<?php
/*
 * Id: $ID$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
imports ( 'kissgo/models/*' );
/**
 *
 * @param Smarty $smarty        	
 */
function set_site_global_vars($smarty) {
    $smarty->assign ( '_SITE_URL', BASE_URL );
    $smarty->assign ( '_PASSPORT', Passport::getPassport () );
    $settings = KissGoSetting::getSetting ();
    $smarty->assign ( '_KISSGO_R_VERSION', $settings ['VERSION'] . ' ' . $settings ['RELEASE'] );
    return $smarty;
}
bind ( 'init_smarty_engine', 'set_site_global_vars' );
/**
 * 设置管理界面全局数据
 *
 * @param Smarty $smarty        	
 */
function set_admin_global_vars($smarty) {
    $smarty->assign ( 'admincp_url', murl ( 'kissgo' ) );
    $smarty->assign ( '_top_navigation_menu', apply_filter ( 'get_top_navigation_menu', new NavigationMenuManager () ) );
    $smarty->assign ( '_foot_toolbar_btns', apply_filter ( 'get_foot_toolbar_buttons', new NavigationFootToolbar () ) );
    return $smarty;
}

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
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-users', __ ( 'Users Management' ), murl ( 'kissgo', 'users' ) );
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-roles', __ ( 'Roles Management' ), murl ( 'kissgo', 'roles' ) );
    $mm->addMenuItemDivider ( 'system' );
    $mm->addMenuItem ( 'system', 'menuitem-modules', __ ( 'Extensions' ), murl ( 'kissgo', 'extension' ), 'icon-asterisk' );
    $mm->addMenuItem ( 'system', 'menuitem-options', __ ( 'Preferences' ), murl ( 'kissgo', 'preference' ), 'icon-wrench' );
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
            $um = new UserEntity ();
            $user = $um->read ( $uid );
            if ($user) {
                $_SESSION ['login_user_info_' . $uid] = $user;
            }
        }
        if ($user) {
            $passport ['name'] = $user ['name'];
            $passport ['email'] = $user ['email'];
        }
    }
    return $passport;
}

bind ( 'get_user_passport', '_kissgo_hook_for_get_user_passport' );
/**
 * load administrator panel page and show it.
 *
 * @param string $tpl
 * @param array $data
 * @return SmartyView
 */
function admin_view($tpl, $data = array(), $headers = array()) {
    bind ( 'init_smarty_engine', 'set_admin_global_vars' );
    return new SmartyView ( $data, $tpl, $headers );
}
// end of __init__.php