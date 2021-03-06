<?php
assert_login ();
function do_admin_attachs_thumb_get($req, $res) {    
    $data ['success'] = false;
    $aid = safe_ids ( rqst ( 'aid' ), ',', true );
    if ($aid) {
        $atM = new KsgAttachmentTable ();
        $atts = $atM->query ( 'attachment_id,url' )->where ( array ('attachment_id IN' => $aid, 'type' => 'image' ) );
        $cnt = 0;
        if ($atts->size ()) {
            $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器
            foreach ( $atts as $att ) {
                $rst = $uploader->thumbnail ( $att ['url'], array (array (80, 60 ), array (260, 180 ), array (300, 200 ) ) );
                $data ['rst'] ['a' . $att ['attachment_id']] = $rst;
            }
        }
        $data ['success'] = true;
    } else {
        $data ['msg'] = "错误的附件编号";
    }
    return new JsonView ( $data );
}