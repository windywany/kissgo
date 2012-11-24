<?php
/**
 * Id: $ID$
 */
defined('KISSGO') or exit ('No direct script access allowed');
/**
 * @param Smarty $smarty
 */
function set_site_global_vars($smarty) {
    $smarty->assign('_SITE_URL', BASE_URL);
    $smarty->assign('_PASSPORT', Passport::getPassport());
    return $smarty;
}

bind('init_smarty_engine', 'set_site_global_vars');
/**
 *
 * 设置管理界面全局数据
 * @param Smarty $smarty
 */
function set_admin_global_vars($smarty) {
    $smarty->assign('admincp_url', BASE_URL . basename(dirname(__FILE__)) . '/');
    $smarty->assign('_kissgo_page_tip', $_SESSION ['_kissgo_page_tip']);
    $smarty->assign('_top_navigation_menu', apply_filter('get_top_navigation_menu', new NavigationMenuManager ()));
    $smarty->assign('_foot_toolbar_btns', apply_filter('get_foot_toolbar_buttons', new NavigationFootToolbar ()));
    unset ($_SESSION ['_kissgo_page_tip']);
    return $smarty;
}

/**
 * 设置导航菜单
 * @param NavigationMenuManager $mm
 * @return mixed
 */
function _hook_for_admincp_menu($mm) {
    $mm->addMenu2('admincp_1', __('System'), 'icon-th-large');
    $mm->addMenuItem('admincp_1', 'book', '小样', '#', 'icon-home');
    $mm->addMenuItemDivider('admincp_1');
    $mm->addMenuItem('admincp_1', 'book1', '大样');
    return $mm;
}

bind('get_top_navigation_menu', '_hook_for_admincp_menu');
/**
 *
 * 设置底部按键
 * @param NavigationFootToolbar $tb
 */
function _hook_for_foot_toolbar($tb) {
    return $tb;
}

bind('get_foot_toolbar_buttons', '_hook_for_foot_toolbar');
function _hook_for_login_page($url) {
    return murl('passport');
}

bind('get_login_page_url_for_KISSGO_ADMIN', '_hook_for_login_page');

function _kissgo_hook_for_get_user_passport($passport) {
    $uid = $passport['uid'];
    if ($uid == 1) {
        $passport['name'] = '宁广丰';
    }
    return $passport;
}

bind('get_user_passport', '_kissgo_hook_for_get_user_passport');
/**
 * 设置页面提示
 * @param $tip
 * @param string $type
 * @param int $during
 */
function set_page_tip($tip, $type = 'info', $during = 5000) {
    $_SESSION ['_kissgo_page_tip'] = array('tip' => $tip, 'type' => $type, 'during' => $during);
}

/**
 * load administrator panel page and show it.
 *
 * @param $tpl
 * @param $data
 * @return SmartyView
 */
function admin_view($tpl, $data = array(), $headers = array()) {
    bind('init_smarty_engine', 'set_admin_global_vars');
    return new SmartyView ($data, $tpl, $headers);
}

/**
 * load the theme view
 *
 * @param $tpl
 * @param array $data
 * @param array $headers
 * @return SmartyView
 */
function theme_view($tpl, $data = array(), $headers = array()) {
    return new SmartyView ($data, $tpl, $headers);
}

/**
 * 显示消息提示页面
 * @param string $type 消息类型
 * @param string $title 提示标题
 * @param string $message 消息内容
 * @param string $redirect 跳转到URL
 * @param int $timeout 跳转时间,当$redirect为空时，些值无效
 */
function show_message($type, $title, $message, $redirect = '', $timeout = 5) {
    echo $type, ':', $title, '<br/>', $message;
    exit();
}

function show_error_message($title, $message, $redirect = '', $timeout = 5) {
    show_error_message('error', $title, $message, $redirect, $timeout);
}
// end of __init__.php