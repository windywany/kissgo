<?php
/**
 * the home page of kissgo gui
 * User: Leo
 * Date: 12-11-2
 * Time: 下午7:43 
 */
assert_login ();
function do_admin_get($req, $res) {
    return view ( 'admin/views/index.tpl' );
}
