<?php
/*
 * 上传
 */
assert_login ();
function do_admin_attachs_upload_get($req, $res) {
    $data ['_CUR_URL'] = murl ( 'admin', 'attachs' );
    return view ( 'admin/views/attachs/upload.tpl', $data );
}