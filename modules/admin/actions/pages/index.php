<?php
/*
 * 页面
 */
assert_login ();
function do_admin_pages_get($req, $res) {
    $data ['_CUR_URL'] = murl ( 'admin', 'pages' );
    return view ( 'admin/views/node/list.tpl', $data );
}