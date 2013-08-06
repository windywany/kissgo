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
        $atM = new KsgAttachmentTable ();
        $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器
        $data ['create_uid'] = $I ['uid'];
        $data ['create_time'] = time ();
        for($i = 0; $i < $count; $i ++) {
            $tmpfile = new UploadTmpFile ( $i, $tmpdir );
            $rst = false;
            if ($tmpfile->isuploaded () && $uploader->allowed ( $tmpfile->file_ext )) {
                $rst = $uploader->save ( $tmpfile );
            }
            if ($rst !== false) { //保存文件成功
                $data ['type'] = $tmpfile->getType ();
                $data ['mine_type'] = $tmpfile->mimeType;
                $data ['url'] = $rst [0];
                $data ['name'] = $tmpfile->title;
                $data ['ext'] = trim ( $tmpfile->file_ext, '.' );
                $data ['alt_text'] = $tmpfile->alt_text;
                $ret = $atM->save ( $data );
                if (count ( $ret ) > 0) {
                    //生成缩略图与添加水印
                    if (in_array ( $tmpfile->file_ext, array ('.jpg', '.gif', '.jpeg', '.png', '.bmp' ) )) {
                        if (cfg ( 'enable_watermark@thumb', false ) && cfg ( 'watermark_pic@thumb' )) {
                            $watermark = APPDATA_PATH . cfg ( 'watermark_pic@thumb' );
                            if (file_exists ( $watermark )) {
                                $uploader->watermark ( $rst [0], $watermark, cfg ( 'watermark_pos@thumb', 'br' ) );
                            }
                        }
                        if (cfg ( 'enable_thumb@thumb', false ) && cfg ( 'thumb_sizes@thumb' )) {
                            $sizestr = cfg ( 'thumb_sizes@thumb' );
                            if (preg_match_all ( '#(\d+?)[x,X](\d+)#', $sizestr, $ss, PREG_SET_ORDER )) {
                                foreach ( $ss as $m ) {
                                    $sizes [] = array ($m [1], $m [2] );
                                }
                                //array (array (80, 60 ), array (260, 180 ), array (300, 200 ) )
                                $uploader->thumbnail ( $rst [0], $sizes );
                            }
                        }
                    }
                } else {
                    $errors [] = "文件上传失败: " . $ret->errorInfo;
                    $uploader->delete ( $rst [1] );
                }
            } else {
                log_error ( "文件上传失败: " . $uploader->get_last_error () );
                $errors [] = "文件上传失败: " . $uploader->get_last_error ();
            }
            $tmpfile->delete ();
        }
    }
    if (count ( $errors ) > 0) {
        show_page_tip ( '<p>' . implode ( '</p><p>', $errors ) . '</p>', 'error' );
    } else {
        show_page_tip ( '文件上传完成.' );
    }
    Response::redirect ( murl ( 'admin', 'media' ) );
}