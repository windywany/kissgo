<?php
/*
 * Plugin ID: cn.usephp.core.gui Plugin Name: KissGO GUI Plugin URI:
 * http://www.usephp.cn/modules/core/ui.html Description: KissGO管理界面。 Author:
 * Leo Ning Version: 1.0 Author URI: http://www.usephp.cn/
 */
/*
 * Id: $ID$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 *
 * @param Smarty $smarty        	
 */
function set_site_global_vars($smarty) {
	$smarty->assign ( '_SITE_URL', BASE_URL );
	$smarty->assign ( '_PASSPORT', Passport::getPassport () );
	return $smarty;
}

bind ( 'init_smarty_engine', 'set_site_global_vars' );
/**
 * 设置管理界面全局数据
 *
 * @param Smarty $smarty        	
 */
function set_admin_global_vars($smarty) {
	$smarty->assign ( 'admincp_url', BASE_URL . basename ( dirname ( __FILE__ ) ) . '/' );
	$smarty->assign ( '_kissgo_page_tip', $_SESSION ['_kissgo_page_tip'] );
	$smarty->assign ( '_top_navigation_menu', apply_filter ( 'get_top_navigation_menu', new NavigationMenuManager () ) );
	$smarty->assign ( '_foot_toolbar_btns', apply_filter ( 'get_foot_toolbar_buttons', new NavigationFootToolbar () ) );
	unset ( $_SESSION ['_kissgo_page_tip'] );
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
	$mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-users', __ ( 'Users Management' ) );
	$mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-groups', __ ( 'Groups Management' ) );
	$mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-roles', __ ( 'Roles Management' ) );
	$mm->addMenuItemDivider ( 'system' );
	$mm->addMenuItem ( 'system', 'menuitem-modules', __ ( 'Plugins & Modules' ), murl ( 'kissgo', 'modules' ), 'icon-asterisk' );
	$mm->addMenuItem ( 'system', 'menuitem-options', __ ( 'Preferences' ), murl ( 'kissgo', 'preferences' ), 'icon-wrench' );
	// Web Site
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
	return $mm;
}
bind ( 'get_top_navigation_menu', '_hook_for_admincp_menu' );
// add new menu items
function _hook_add_new_menu_items($items) {
	$items .= '<li><a href="#">new page</a></li>';
	return $items;
}
bind ( 'add_new_menu_items', '_hook_add_new_menu_items' );
function _hook_for_add_passport_menu_items($items) {
	$items .= '<li><a href="#">Control Panel</a></li>';
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
function _hook_for_login_page($url) {
	return murl ( 'passport' );
}

bind ( 'get_login_page_url_for_KISSGO_ADMIN', '_hook_for_login_page' );
function _kissgo_hook_for_get_user_passport($passport) {
	$uid = $passport ['uid'];
	if ($uid == 1) {
		$passport ['name'] = '宁广丰';
	}
	return $passport;
}

bind ( 'get_user_passport', '_kissgo_hook_for_get_user_passport' );
/**
 * 设置页面提示
 *
 * @param
 *        	$tip
 * @param string $type        	
 * @param int $during        	
 */
function set_page_tip($tip, $type = 'info', $during = 5000) {
	$_SESSION ['_kissgo_page_tip'] = array (
			'tip' => $tip,
			'type' => $type,
			'during' => $during 
	);
}

/**
 * load administrator panel page and show it.
 *
 * @param
 *        	$tpl
 * @param
 *        	$data
 * @return SmartyView
 */
function admin_view($tpl, $data = array(), $headers = array()) {
	bind ( 'init_smarty_engine', 'set_admin_global_vars' );
	return new SmartyView ( $data, $tpl, $headers );
}
// end of __init__.php