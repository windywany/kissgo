<?php
/**
 * Id: $ID$
 */
/**
 * @param Smarty $smarty
 */
function set_site_global_vars($smarty) {
    $smarty->assign('_SITE_URL', BASE_URL);
    return $smarty;
}

bind('init_smarty_engine', 'set_site_global_vars');
function set_admin_global_vars($smarty) {
    $smarty->assign('admincp_url', BASE_URL . basename(dirname(__FILE__)) . '/');
    $smarty->assign('_kissgo_page_tip', $_SESSION['_kissgo_page_tip']);
    unset($_SESSION['_kissgo_page_tip']);
    return $smarty;
}

/**
 * 设置页面提示
 * @param $tip
 * @param string $type
 * @param int $during
 */
function set_page_tip($tip, $type = 'info', $during = 5000) {
    $_SESSION['_kissgo_page_tip'] = array('tip' => $tip, 'type' => $type, 'during' => $during);
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
    return new SmartyView($data, $tpl, $headers);
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
    return new SmartyView($data, $tpl, $headers);
}

function show_error_message() {

}

I18n::append(__FILE__);

