<?php
assert_login ();
/**
 * 
 * clear Inner Cache
 */
function do_admin_cache_clear($req, $res, $type = '') {
    InnerCacher::clear ();
    show_page_tip ( "The Cache Cleared!" );
    Response::back ();
}