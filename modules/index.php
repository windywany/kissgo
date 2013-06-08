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
    $url = Request::getVirtualPageUrl ();
    $frontPage = FrontPage::initWithPageURL ( $url );
    if ($frontPage) {
        $tpl = $frontPage->getTemplate ();
        $data = $frontPage->toArray ();
        $data ['template'] = $tpl;
        return template ( $tpl, $data );
    }
    return null;
}
// end of index.php