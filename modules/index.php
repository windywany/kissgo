<?php
/*
 * You don't need to change this script logics, just handle the 'after_route' hook.
 * 
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
function _kissgo_default_index($view) {
    $url = Request::getVirtualPageUrl ();
    if ($view == null && ($url == '/' || $url == '/index.html')) {
        return template ( 'index.tpl' );
    }
    return $view;
}
/**
 * 
 * 显示静态页面
 */
function do_show_custom_page() {
    bind ( 'after_route', '_kissgo_default_index', 10000 );
    $view = apply_filter ( 'after_route', NULL );
    if ($view == NULL) {
        $view = get_view_for_page ();
        if ($view != null) {
            return $view;
        }
        Response::respond ( 404 );
    } else {
        return $view;
    }
}
/**
 * 
 * 取当前页面的视图
 */
function get_view_for_page() {
    global $_CURRENT_NODE;
    $url = Request::getVirtualPageUrl ();
    $frontPage = FrontPage::initWithPageURL ( $url );
    if ($frontPage) {
        $data = $frontPage->toArray ();
        if ($data ['status'] == 'published' || canpreview ()) {
            $tpl = $frontPage->getTemplate ();
            $tpl = get_prefer_tpl ( $tpl, $data );
            $data ['template'] = $tpl;
            $_CURRENT_NODE = $data;
            return template ( $tpl, $data );
        }
    }
    return null;
}
function canpreview() {
    $I = whoami ();
    // TODO need check the user's right
    return $I->isLogin () && isset ( $_GET ['preview'] );
}
// end of index.php