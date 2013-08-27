<?php
/*
 * dashboard menu hooks
 * @author guangfeng Ning <windywany@163.com>
 */
// -----------------------------------------------------------------------
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 设置导航菜单
 * applier for 'get_top_navigation_menu'
 * @param NavigationMenuManager $mm        	
 * @return NavigationMenuManager
 */
function hook_for_admincp_menu($mm) {
    // System
    $mm->addMenu2 ( 'system', __ ( 'System' ), 'icon-th-large' );
    $mm->addMenuItem ( 'system', 'menuitem-user-role', __ ( 'Users & Roles' ), murl ( 'admin', 'users' ), 'icon-user' );
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-users', __ ( 'Users Management' ), murl ( 'admin', 'users' ), 'icon-user' );
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-roles', __ ( 'Roles Management' ), murl ( 'admin', 'roles' ), 'icon-user' );
    $mm->addMenuItemDivider ( 'system' );
    $mm->addMenuItem ( 'system', 'menuitem-modules', __ ( 'Extensions' ), murl ( 'admin', 'extension' ), 'icon-briefcase' );
    $mm->addMenuItem ( 'system', 'menuitem-options', __ ( 'Preferences' ), murl ( 'admin', 'preference' ), 'icon-adjust' );
    $mm->addMenuItem ( 'system', 'menuitem-inner-clear', __ ( 'Clear Inner Cache' ), murl ( 'admin', 'cache/clear' ), 'icon-trash' );
    $mm->addMenuItemDivider ( 'system' );
    $mm->addMenuItem ( 'system', 'menuitem-cm-builder', __ ( 'Model Builder' ), murl ( 'admin', 'cmb' ), 'icon-tint' );
    // Web Site
    $mm->addMenu2 ( 'menu-website', __ ( 'Site' ), 'icon-globe' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-pages', __ ( 'Web Pages' ), murl ( 'admin', 'pages' ), 'icon-file' );
    
    $mm->addSubItem ( 'menu-website/menuitem-pages', 'menuitem-pages-list', __ ( 'Draft' ), murl ( 'admin', 'pages' ), 'icon-file' );
    $mm->addSubItem ( 'menu-website/menuitem-pages', 'menuitem-pages-list1', __ ( 'Approving' ), murl ( 'admin', 'pages/approving' ), 'icon-star-empty' );
    $mm->addSubItem ( 'menu-website/menuitem-pages', 'menuitem-pages-list2', __ ( 'Approved' ), murl ( 'admin', 'pages/approved' ), 'icon-thumbs-up' );
    $mm->addSubItem ( 'menu-website/menuitem-pages', 'menuitem-pages-list3', __ ( 'Published' ), murl ( 'admin', 'pages/published' ), 'icon-check' );
    $mm->addSubItem ( 'menu-website/menuitem-pages', 'menuitem-pages-list4', __ ( 'Unapproved' ), murl ( 'admin', 'pages/unapproved' ), 'icon-thumbs-down' );
    $mm->addSubItem ( 'menu-website/menuitem-pages', 'menuitem-pages-list5', __ ( 'Recycle Bin' ), murl ( 'admin', 'pages/trash' ), 'icon-trash' );
    $mm->addMenuItemDivider ( 'menu-website/menuitem-pages' );
    $mm->addSubItem ( 'menu-website/menuitem-pages', 'menuitem-pagetypes', __ ( 'Page Types' ), murl ( 'admin', 'pages/type' ), 'icon-list' );
    
    $mm->addMenuItem ( 'menu-website', 'menuitem-comments', __ ( 'Comments' ), murl ( 'admin', 'comments' ), 'icon-comment' );
    $mm->addSubItem ( 'menu-website/menuitem-comments', 'menuitem-cmt-list', __ ( 'New Comments' ), murl ( 'admin', 'comments' ), 'icon-comment' );
    $mm->addSubItem ( 'menu-website/menuitem-comments', 'menuitem-cmt-list1', __ ( 'Approved' ), murl ( 'admin', 'comments/pass' ), 'icon-thumbs-up' );
    $mm->addSubItem ( 'menu-website/menuitem-comments', 'menuitem-cmt-list2', __ ( 'Unapproved' ), murl ( 'admin', 'comments/unpass' ), 'icon-thumbs-down' );
    $mm->addSubItem ( 'menu-website/menuitem-comments', 'menuitem-cmt-list3', __ ( 'Spam Comments' ), murl ( 'admin', 'comments/spam' ), 'icon-fire' );
    $mm->addSubItem ( 'menu-website/menuitem-comments', 'menuitem-cmt-list4', __ ( 'Recycle Bin' ), murl ( 'admin', 'comments/trash' ), 'icon-trash' );
    
    $mm->addMenuItem ( 'menu-website', 'menuitem-tags', __ ( 'Tags&Flags' ), murl ( 'admin', 'tags' ), 'icon-tags' );
    $mm->addMenuItemDivider ( 'menu-website' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-theme', __ ( 'Theme' ), murl ( 'admin', 'theme' ), 'icon-picture' );
    
    $mm->addMenuItem ( 'menu-website', 'menuitem-menus', __ ( 'Menus' ), murl ( 'admin', 'menus' ), 'icon-list' );
    $mm->addMenuItemDivider ( 'menu-website' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-medias', __ ( 'Medias' ), murl ( 'admin', 'media' ), 'icon-picture' );
    // Contents
    $mm->addMenu2 ( 'contents', __ ( 'Contents' ), 'icon-th-list' );
    $mm->addMenuItem ( 'contents', 'cms-article', __ ( 'Article' ), murl ( 'admin', 'article' ), 'icon-list-alt' );
    $mm->addSubItem ( 'contents/cms-article', 'menuitem-article-list', __ ( 'List Article' ), murl ( 'admin', 'article' ), 'icon-list-alt' );
    $mm->addSubItem ( 'contents/cms-article', 'menuitem-article-add', __ ( 'Create Article' ), murl ( 'admin', 'article/add' ), 'icon-file' );
    
    return $mm;
}
/**
 * 
 * applier for 'add_new_menu_items'
 * @param string $items
 */
function hook_add_new_menu_items($items) {
    $items .= '<li><a href="#" class="ksg-publish" data-type="catalog"><i class="icon-folder-open"></i> ' . __ ( 'Virtual Directory' ) . '</a></li>';
    $items .= '<li><a href="'.murl ( 'admin', 'article/add' ).'"><i class="icon-file"></i> ' . __ ( 'Article' ) . '</a></li>';
    return $items;
}
/**
 * 
 * applier for 'add_passport_menu_items'
 * @param string $items
 */
function hook_for_add_passport_menu_items($items) {
    $items .= '<li><a href="' . murl ( 'admin', 'account' ) . '"><i class="icon-user"></i> ' . __ ( 'Control Panel' ) . '</a></li>';
    return $items;
}

/**
 * 
 * 读取登录用户信息
 * applier for 'get_user_passport'
 * @param Passport $passport
 * @return Passport
 */
function kissgo_hook_for_get_user_passport($passport) {
    if ($passport->isLogin ()) {
        $uid = $passport ['uid'];
        $user = sess_get ( 'login_user_info_' . $uid, false );
        if (! $user) {
            $um = new KsgUserTable ();
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