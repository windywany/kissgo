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
    global $_ksg_router_url;
    $settings = KissGoSetting::getSetting ();
    $smarty->assign ( '_ksg_base_url', BASE_URL );
    $smarty->assign ( '_ksg_passport', Passport::getPassport () );
    $smarty->assign ( '_ksg_version', KISSGO_VERSION );
    $smarty->assign ( '_ksg_rversion', KISSGO_R_VERSION );
    $smarty->assign ( '_ksg_page_uri', Request::getUri () );
    $smarty->assign ( '_ksg_page_url', Request::getVirtualPageUrl () );
    $smarty->assign ( '_ksg_router_base', clean_url () );
    $smarty->assign ( 'ROUTER_URL', $_ksg_router_url );
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// register hooks
bind ( 'get_top_navigation_menu', array ('hook_for_admincp_menu', 'admin/callbacks/menu_hooks.php' ) );
bind ( 'add_new_menu_items', array ('hook_add_new_menu_items', 'admin/callbacks/menu_hooks.php' ) );
bind ( 'add_passport_menu_items', array ('hook_for_add_passport_menu_items', 'admin/callbacks/menu_hooks.php' ) );
bind ( 'get_user_passport', array ('kissgo_hook_for_get_user_passport', 'admin/callbacks/menu_hooks.php' ) );
bind ( 'after_save_node_for_plain', array ('after_save_node_for_plain', 'admin/callbacks/article_hooks.php' ) );
// ajax hooks
bind ( 'do_ajax_ajax_validate', array ('do_ajax_validate_check', 'admin/callbacks/do_ajax.php' ) );
bind ( 'do_ajax_browser_template_files', array ('do_ajax_browser_template_files', 'admin/callbacks/do_ajax.php' ) );
bind ( 'do_ajax_browser_all_template_files', array ('do_ajax_browser_all_template_files', 'admin/callbacks/do_ajax.php' ) );
bind ( 'do_ajax_tags_autocomplete', array ('do_ajax_tags_autocomplete', 'admin/callbacks/do_ajax.php' ) );
bind ( 'do_ajax_nodes_autocomplete', array ('do_ajax_nodes_autocomplete', 'admin/callbacks/do_ajax.php' ) );
bind ( 'do_ajax_test_email', array ('do_ajax_test_email', 'admin/callbacks/do_ajax.php' ) );
bind ( 'do_ajax_browser_menus', array ('do_ajax_browser_menus', 'admin/callbacks/do_ajax.php' ) );
bind ( 'do_ajax_images_autocomplete', array ('do_ajax_images_autocomplete', 'admin/callbacks/do_ajax.php' ) );

// end register hooks
///////////////////////////////////////////////////////////////////////////////////////////////////////////
// register content providers
register_cts_provider ( 'menu', array ('cts_pd_menu', 'admin/callbacks/cts_pd.php' ), 'build the navigator data' );
register_cts_provider ( 'pages', array ('cts_pd_pages', 'admin/callbacks/cts_pd.php' ), 'retreives pages which satisfies the options' );
/////////////////////////////////////////////////////////////////////////////////////////////////////////
// end of __init__.php