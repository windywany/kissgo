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
        $data ['crumb'] = array (array ('mid' => 0, 'name' => 'Home', 'url' => BASE_URL, 'title' => cfg ( 'site_name', '' ) ) );
        $data ['mid'] = 0;
        return template ( 'index.tpl', $data );
    } else if (preg_match ( '#.+style\.css$#', $url )) {
        return merge_css ( $url );
    } else if (preg_match ( '#.+script\.js$#', $url )) {
        return merge_js ();
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
    $url = Request::parseURL ();
    if ($url) {
        $frontPage = FrontPage::initWithPageURL ( $url ['name'] );
        if ($frontPage) {
            $data = $frontPage->toArray ();
            if ($data ['status'] == 'published' || canpreview ()) {
                $tpl = $frontPage->getTemplate ();
                $tpl = get_prefer_tpl ( $tpl, $data );
                $data ['template'] = $tpl;
                $_CURRENT_NODE = $data;
                return template ( $tpl, $data );
            } else {
                log_warn ( "you don't have permission to view:" . Request::getUri () );
                Response::respond ( 403 );
            }
        } else {
            log_warn ( "Kissgo cannot process url:" . Request::getUri () );
        }
    } else {
        log_warn ( "Kissgo cannot process url:" . Request::getUri () );
        Response::respond ( 500 );
    }
    return null;
}
function canpreview() {
    $I = whoami ();
    // TODO need check the user's right
    return $I->isLogin () && isset ( $_GET ['preview'] );
}
/**
 * 
 * merge css files
 * @param string $path
 */
function merge_css($path) {
    $f = rqst ( 'f' );
    if (! $f) {
        return '';
    }
    $etag = md5 ( $path . $f );
    $etag_m = $etag . '_m';
    if (isset ( $_SERVER ['HTTP_IF_NONE_MATCH'] ) && $_SERVER ['HTTP_IF_NONE_MATCH'] == $etag) {
        $lastModified = strtotime ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] );
        $etagM = InnerCacher::get ( $etag_m );
        if ($lastModified && $etagM && $lastModified >= $etagM) {
            status_header ( 304 );
            @header ( 'Etag: ' . $etag );
            @header ( 'Last-Modified: ' . $_SERVER ['HTTP_IF_MODIFIED_SINCE'] );
            Response::getInstance ()->close ();
        } else if (! $etagM) {
            InnerCacher::remove ( $etag );
        }
    }
    
    $styles = InnerCacher::get ( $etag );
    if (! $styles) {
        $path = WEB_ROOT . ltrim ( pathinfo ( $path, PATHINFO_DIRNAME ), '/' ) . '/';
        $styles = '';
        $fs = explode ( ',', $f );
        if ($fs) {
            foreach ( $fs as $f ) {
                $f = $path . $f . '.css';
                if (is_file ( $f )) {
                    $styles .= @file_get_contents ( $f );
                }
            }
        }
        InnerCacher::add ( $etag, $styles );
        InnerCacher::add ( $etag_m, time () );
    }
    return new CssView ( $styles, $etag );
}
/**
 * 
 * merge js files
 */
function merge_js() {
    $jss = rqst ( 'f', '' );
    if ($jss) {
        $etag = md5 ( $jss );
        $etag_m = $etag . '_m';
        if (isset ( $_SERVER ['HTTP_IF_NONE_MATCH'] ) && $_SERVER ['HTTP_IF_NONE_MATCH'] == $etag) {
            $lastModified = strtotime ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] );
            $etagM = InnerCacher::get ( $etag_m );
            if ($lastModified && $etagM && $lastModified >= $etagM) {
                status_header ( 304 );
                @header ( 'Etag: ' . $etag );
                @header ( 'Last-Modified: ' . $_SERVER ['HTTP_IF_MODIFIED_SINCE'] );
                Response::getInstance ()->close ();
            } else if (! $etagM) {
                InnerCacher::remove ( $etag );
            }
        }
        $jsses = InnerCacher::get ( $etag );
        if (! $jsses) {
            $jsses = View::getCommonJS ();
            $js = '';
            $prefix = WEB_ROOT . WEBSITE_DIR . DS;
            $fss = explode ( ';', $jss );
            foreach ( $fss as $_fs ) {
                if (preg_match ( '#(.*?)\{(.+)\}#', $_fs, $m )) {
                    $fs = explode ( ',', $m [2] );
                    if ($fs) {
                        foreach ( $fs as $f ) {
                            $js = $prefix . $m [1] . DS . $f . '.js';
                            if (is_file ( $js )) {
                                $jsses .= @file_get_contents ( $js ) . ";\n";
                            }
                        }
                        InnerCacher::add ( $etag, $jsses );
                        InnerCacher::add ( $etag_m, time () );
                    }
                }
            }
        }
        return new JsView ( $jsses, $etag );
    }
    return '';
}
// end of index.php