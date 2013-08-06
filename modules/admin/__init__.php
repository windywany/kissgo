<?php
/*
 * Id: $ID$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
imports ( 'admin/validator_callbacks.php' );
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
    $mm->addMenuItem ( 'system', 'menuitem-user-role', __ ( 'Users & Roles' ), murl ( 'admin', 'users' ), 'icon-user' );
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-users', __ ( 'Users Management' ), murl ( 'admin', 'users' ), 'icon-user' );
    $mm->addSubItem ( 'system/menuitem-user-role', 'submenu-item-roles', __ ( 'Roles Management' ), murl ( 'admin', 'roles' ), 'icon-user' );
    $mm->addMenuItemDivider ( 'system' );
    $mm->addMenuItem ( 'system', 'menuitem-modules', __ ( 'Extensions' ), murl ( 'admin', 'extension' ), 'icon-briefcase' );
    $mm->addMenuItem ( 'system', 'menuitem-options', __ ( 'Preferences' ), murl ( 'admin', 'preference' ), 'icon-adjust' );
    $mm->addMenuItemDivider ( 'system' );
    $mm->addMenuItem ( 'system', 'menuitem-cm-builder', __ ( 'Model Builder' ), murl ( 'admin', 'cmb' ), 'icon-tint' );
    // Web Site
    $mm->addMenu2 ( 'menu-website', __ ( 'Site' ), 'icon-globe' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-pages', __ ( 'Web Pages' ), murl ( 'admin', 'pages' ), 'icon-file' );
    
    $mm->addMenuItem ( 'menu-website', 'menuitem-comments', __ ( 'Comments' ), murl ( 'admin', 'pages/comments' ), 'icon-comment' );
    
    $mm->addMenuItem ( 'menu-website', 'menuitem-tags', __ ( 'Tags&Flags' ), murl ( 'admin', 'tags' ), 'icon-tags' );
    $mm->addMenuItemDivider ( 'menu-website' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-theme', __ ( 'Theme' ), murl ( 'admin', 'node/theme' ), 'icon-picture' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-pagetypes', __ ( 'Page Types' ), murl ( 'admin', 'node/type' ), 'icon-list' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-menus', __ ( 'Menus' ), murl ( 'admin', 'menus' ), 'icon-list' );
    $mm->addMenuItemDivider ( 'menu-website' );
    $mm->addMenuItem ( 'menu-website', 'menuitem-attachs', __ ( 'Medias' ), murl ( 'admin', 'media' ), 'icon-picture' );
    // Components
    $mm->addMenu2 ( 'menu-components', __ ( 'Components' ), 'icon-cog' );
    $mm->addMenuItem ( 'menu-components', 'menuitem-codes', __ ( 'Code Fragments' ), murl ( 'admin', 'fragments' ), 'icon-list-alt' );
    $mm->addMenuItem ( 'menu-components', 'menuitem-links', __ ( 'Links' ), murl ( 'admin', 'links' ), 'icon-retweet' );
    return $mm;
}
bind ( 'get_top_navigation_menu', '_hook_for_admincp_menu' );
function _hook_add_new_menu_items($items) {
    $items .= '<li><a href="#"><i class="icon-file"></i> 假的</a></li>';
    return $items;
}
bind ( 'add_new_menu_items', '_hook_add_new_menu_items' );
function _hook_for_add_passport_menu_items($items) {
    $items .= '<li><a href="' . murl ( 'admin', 'account' ) . '"><i class="icon-user"></i> ' . __ ( 'Control Panel' ) . '</a></li>';
    return $items;
}
bind ( 'add_passport_menu_items', '_hook_for_add_passport_menu_items' );

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

bind ( 'get_user_passport', '_kissgo_hook_for_get_user_passport' );
//ajax validate
function do_ajax_validate_check($req) {
    if (! isset ( $req ['__cb'] )) {
        echo 'false';
    } else {
        $cb = $req ['__cb'];
        $rst = false;
        if (is_callable ( $cb )) {
            $rst = call_user_func_array ( $cb, array (null, $req, null ) );
        }
        if ($rst === true) {
            echo 'true';
        } else if ($rst === false) {
            echo 'false';
        } else {
            echo $rst;
        }
    }
}
bind ( 'do_ajax_ajax_validate', 'do_ajax_validate_check' );
// 浏览模板文件
function do_ajax_browser_template_files($req) {
    $theme = rqst ( 'theme', THEME );
    $id = rqst ( 'id', '' );
    $path = THEME_PATH . THEME_DIR . DS . $theme . $id;
    $hd = opendir ( $path );
    $dirs = array ();
    $files = array ();
    if ($hd) {
        while ( ($f = readdir ( $hd )) != false ) {
            if (is_dir ( $path . DS . $f ) && $f != '.' && $f != '..' && $f != 'admin') {
                $dirs [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => true );
            }
            if (is_file ( $path . DS . $f ) && preg_match ( '/.+\.tpl$/', $f )) {
                $files [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => false );
            }
        }
        closedir ( $hd );
        ksort ( $dirs );
        ksort ( $files );
        $dirs = $dirs + $files;
        $dirs = array_values ( $dirs );
    }
    echo json_encode ( $dirs );
}
bind ( 'do_ajax_browser_template_files', 'do_ajax_browser_template_files' );
// 读取标签
function do_ajax_tags_autocomplete($req) {
    $q = rqst ( 'q', '' );
    $p = irqst ( 'p', 1 );
    $tagTable = new KsgTagTable ();
    $more = true;
    $where = array ('type' => 'tag' );
    $tags = $tagTable->query ( 'TG.tag_id as id, tag as text', 'TG' );
    if (empty ( $q )) {
        $more = false;
    } else {
        $where ['tag LIKE'] = "%{$q}%";
    }
    $nodeTagTable = new KsgNodeTagsTable ();
    $hots = $nodeTagTable->query ( imtf ( "COUNT(NT.tag_id)", 'total' ), 'NT' )->where ( array ('NT.tag_id' => imtv ( 'TG.tag_id' ) ) );
    $tags->field ( $hots, 'hots' );
    $tags->where ( $where )->limit ( $p, 10 )->sort ( 'hots', 'd' );
    if ($more) {
        $more = $tags->size () > 0;
    }
    $data = array ('more' => $more, 'results' => $tags->toArray () );
    echo json_encode ( $data );
}
bind ( 'do_ajax_tags_autocomplete', 'do_ajax_tags_autocomplete' );

// 读取页面
function do_ajax_nodes_autocomplete($req) {
    $q = rqst ( 'q', '' );
    $p = irqst ( 'p', 1 );
    $nodeTable = new KsgNodeTable ();
    $more = true;
    $where = array ('deleted' => 0, 'status' => 'published' );
    $nodes = $nodeTable->query ( 'nid as id, title as text' );
    if (empty ( $q )) {
        $more = false;
    } else {
        $where ['title LIKE'] = "%{$q}%";
    }
    $nodes->where ( $where )->limit ( $p, 10 )->sort ( 'publish_time', 'd' );
    if ($more) {
        $more = $nodes->size () > 0;
    }
    $data = array ('more' => $more, 'results' => $nodes->toArray () );
    echo json_encode ( $data );
}
bind ( 'do_ajax_nodes_autocomplete', 'do_ajax_nodes_autocomplete' );

//测试邮件发送功能
function do_ajax_test_email($params) {
    KissGo::startSession ();
    $I = whoami ();
    if ($I->isLogin ()) {
        $message = "亲爱的网站管理员：\n\t这是一封测试邮件，如果你能收到，则说明你网站中邮件配置是正确的。\n\n此邮件只用于测试，请不要回复。\n\n请尽情享受KissGO! CMS吧。";
        $email = rqst ( 'email' );
        $rst = sendmail ( $email, "恭喜,你网站的邮件配置通过", $message, null, 'text' );
        if ($rst) {
            $data ['msg'] = '邮件已经发出,请查收.';
        } else {
            $data ['msg'] = '邮件发送失败,详情请查看日志.';
        }
    } else {
        show_error_message ( "你没登录，无权操作." );
    }
    echo json_encode ( $data );
}
bind ( 'do_ajax_test_email', 'do_ajax_test_email' );
// end of __init__.php