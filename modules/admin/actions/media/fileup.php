<?php
/**
 * 上传附件（编辑器）
 * 
 * @param Request $req
 * @param Respone $res
 */
function do_admin_media_fileup($req, $res) {
    $I = assert_login ();
    $title = $req ['pictitle'];
    $upfile = $_FILES ['upfile'];
    $i ['tmpname'] = $upfile ['tmp_name'];
    $i ['filesize'] = $upfile ['size'];
    $i ['name'] = $upfile ['name'];
    $i ['title'] = $title;
    $i ['alt'] = $title;
    $tmpFile = new UploadTmpFile ( $i, false );
    $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器    
    $rst = $tmpFile->save ( $uploader, $I ['uid'] );
    
    $data ['fileType'] = $tmpFile->file_ext;
    $data ['original'] = $tmpFile->orName;
    
    if ($rst) {
        $data ['url'] = $rst ['url'];
        $data ['state'] = 'SUCCESS';
    } else {
        $data ['state'] = $tmpFile->errorInfo ();
    }
    return new JsonView ( $data );
}