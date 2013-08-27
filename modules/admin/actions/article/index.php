<?php
assert_login ();

function do_admin_article_get($req, $res, $status = 'draft') {
    $status_ary = array ('draft', 'published' );
    if (! in_array ( $status, $status_ary )) {
        $status = 'draft';
    }
    
    $data = array ();
    $data ['status'] = $status;
    return view ( 'admin/views/article/index.tpl', $data );
}