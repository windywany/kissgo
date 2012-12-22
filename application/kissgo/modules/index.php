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
    if ($view == null && Request::getVirtualPageUrl () == '/') {
        return theme_view ( 'index.tpl' );
    }
    return $view;
}
bind ( 'after_route', '_kissgo_default_index', 10000 );
$view = apply_filter ( 'after_route', NULL );
if ($view == NULL) {
    Response::respond ( 404 );
} else {
    return $view;
}
// end of index.php