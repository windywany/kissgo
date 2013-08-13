<?php
assert_login ();
/**
 * 
 * clear Inner Cache
 */
function do_admin_cache_clear($type = 'inner') {
    InnerCacher::clear ();
    show_page_tip ( "The Cache Cleared!" );
    Response::back ();
}