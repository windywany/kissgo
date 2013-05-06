<?php
/*
 * 创建页面
 */
assert_login ();
function do_admin_pages_create_get($req, $res) {
    $data ['_CUR_URL'] = murl ( 'admin', 'pages/create' );
    if (! isset ( $req ['type'] )) {
        show_page_tip ( '<strong>Oops!</strong>出错啦:无效的页面类型.' );
        Response::back ();
    }
    $type = $req ['type'];
    $data ['type'] = $type;
    return view ( 'admin/views/node/editor/editor.tpl', $data );
}