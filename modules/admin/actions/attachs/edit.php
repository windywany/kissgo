<?php
/**
 * 编辑修改
 */
assert_login ();
function do_admin_attachs_edit_get($req, $res) {    
    $data = array ('success' => false );
    $aid = irqst ( 'aid' );
    $name = rqst ( 'name' );
    $alt = rqst ( 'alt' );
    if ($aid) {
        $att ['name'] = $name;
        $att ['alt_text'] = $alt;
        $atM = new KsgAttachmentTable ();
        $saver = $atM->save ( $att );
        $saver->where ( array ('attachment_id' => $aid ) );
        if (count ( $saver ) >= 0) {
            $data ['success'] = true;
        } else {
            $data ['msg'] = '出错了: 数据库操作出错。';
        }
    } else {
        $data ['msg'] = '未指定编号';
    }
    return new JsonView ( $data );
}