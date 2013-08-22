<?php
/**
 * get uploading page
 */
assert_login ();
function do_admin_media_upload_get($req, $res) {
    $data ['_CUR_URL'] = murl ( 'admin', 'media' );
    return view ( 'admin/views/media/upload.tpl', $data );
}
/**
 * finish uploading
 * @param unknown_type $req
 * @param unknown_type $res
 */
function do_admin_media_upload_post($req, $res) {
    $tmpdir = TMP_PATH . "plupload";
    $count = irqst ( 'uploader_count', 0 );
    $errors = array ();
    $I = whoami ();
    if ($count > 0) {
        @set_time_limit ( 0 );
        $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器        
        for($i = 0; $i < $count; $i ++) {
            $tmpfile = new UploadTmpFile ( $i, $tmpdir );
            $rst = $tmpfile->save ( $uploader, $I ['uid'] );
            if (! $rst) {
                $errors += $tmpfile->errors;
            }
        }
    }
    if (count ( $errors ) > 0) {
        show_page_tip ( '<p>' . implode ( '</p><p>', $errors ) . '</p>', 'error' );
    } else {
        show_page_tip ( '文件上传完成.' );
    }
    
    Response::redirect ( murl ( 'admin', 'media' ) );
}