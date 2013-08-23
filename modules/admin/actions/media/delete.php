<?php
assert_login ();
function do_admin_media_delete_get($req, $res) {    
    $aids = safe_ids ( rqst ( 'aid' ), ',', true );
    if (! empty ( $aids )) {
        $atM = new MediaTable();        
        $atts = $atM->query ( 'fid,url' )->where ( array ('fid IN' => $aids ) );
        if ($atts->size () > 0) {
            $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器
            $ids = array ();
            foreach ( $atts as $att ) {
                if ($att && $uploader->delete ( $att ['url'] )) {
                    $ids [] = $att ['fid'];
                }
            }
            if ($ids) {
                $deletor = $atM->delete ()->where ( array ('fid IN' => $ids ) );
                count ( $deletor );
            }
        }
    }
    Response::back ();
}