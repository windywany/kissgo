<?php
/**
 * the home page of kissgo gui
 * User: Leo
 * Date: 12-11-2
 * Time: 下午7:43 
 */
assert_login ();
function do_kissgo_index($req, $res) {
    return view ( 'kissgo/views/index.tpl' );
}
