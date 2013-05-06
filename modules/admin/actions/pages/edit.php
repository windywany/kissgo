<?php
/*
 * 编辑页面
*/
assert_login ();
function do_admin_pages_edit_get($req, $res) {
    $data ['_CUR_URL'] = murl ( 'admin', 'pages/edit' );
    $pid = irqst ( 'pid' );
    if (empty ( $pid )) {
        show_page_tip ( '<strong>Oops!</strong>出错啦:无效的页面编号.' );
        Response::back ();
    }
    $nodeTable = new NodeTable ();
    $node = $nodeTable->read ( array ('nid' => $pid ) );
    if (empty ( $node )) {
        show_page_tip ( '<strong>Oops!</strong>出错啦:页面不存在.' );
        Response::back ();
    }
    $data ['node'] = $node;
    $data ['type'] = $node ['node_type'];
    return view ( 'admin/views/node/editor/editor.tpl', $data );
}