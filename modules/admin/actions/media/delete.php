<?php
assert_login ();
function do_admin_media_delete_get($req, $res) {    
    $aids = safe_ids ( rqst ( 'aid' ), ',', true );
    if (! empty ( $aids )) {
        $atM = new KsgAttachmentTable ();
        $atM = new KsgAttachmentTable ();
        $atts = $atM->query ( 'attachment_id,url' )->where ( array ('attachment_id IN' => $aids ) );
        if ($atts->size () > 0) {
            $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器
            $ids = array ();
            foreach ( $atts as $att ) {
                if ($att && $uploader->delete ( $att ['url'] )) {
                    $ids [] = $att ['attachment_id'];
                }
            }
            if ($ids) {
                $deletor = $atM->delete ()->where ( array ('attachment_id IN' => $ids ) );
                count ( $deletor );
            }
        }
    }
    Response::back ();
}