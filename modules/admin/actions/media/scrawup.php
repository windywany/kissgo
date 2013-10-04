<?php
/**
 * 上传涂鸦图片（编辑器）
 *
 * @param Request $req
 * @param Respone $res
 */
function do_admin_media_scrawup($req, $res) {
    $I = assert_login ();
    $action = $req ['action'];
    if ($action == "tmpImg") {
        $title = 'scrawup-back-' . time ();
        $upfile = $_FILES ['upfile'];
        $i ['tmpname'] = $upfile ['tmp_name'];
        $i ['filesize'] = $upfile ['size'];
        $i ['name'] = $upfile ['name'];
        $i ['title'] = $title;
        $i ['alt'] = $title;
        $tmpFile = new UploadTmpFile ( $i, false );
        $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器
        $rst = $tmpFile->save ( $uploader, $I ['uid'] );
        if ($rst) {
            return "<script>parent.ue_callback('" . $rst ["url"] . "','SUCCESS')</script>";
        } else {
            $data ['state'] = $tmpFile->errorInfo ();
            return "<script>parent.ue_callback('','" . $tmpFile->errorInfo () . "')</script>";
        }
    } else {
        $title = '涂鸦-' . date ( 'Y-m-d H:i:s' );
        $i ['name'] = 'content';
        $i ['title'] = $title;
        $i ['alt'] = $title;
        $tmpFile = new UploadTmpFile ( $i, false, false, true );
        $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器
        $rst = $tmpFile->save ( $uploader, $I ['uid'] );
        
        if ($rst) {
            $data ['url'] = $rst ['url'];
            $data ['state'] = 'SUCCESS';
        } else {
            $data ['url'] = '';
            $data ['state'] = $tmpFile->errorInfo ();
        }
        
        return new JsonView ( $data );
    }
}