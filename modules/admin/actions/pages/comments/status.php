<?php
/*
 * 设置评论的状态
 */
assert_login ();
function do_admin_pages_comments_status_get($req, $res) {
    $status = array ('new', 'pass', 'unpass', 'spam' );
    $ids = rqst ( 'cid' );
    $ids = safe_ids ( $ids, ",", true );
    if (empty ( $ids )) {
        show_page_tip ( '<strong>Oops!</strong><br/>错误的评论编号', 'error' );
        Response::back ();
    }
    $cmM = new NodeCommentTable ();
    if (isset ( $req ['del'] )) {
        $del = irqst ( 'del' );
        $deleted = $del ? 1 : 0;
        $rst = $cmM->update ( array ('deleted' => $deleted ), array ('id IN' => $ids ) );
    } else {
        $s = rqst ( 's', 'new' );
        $s = in_array ( $s, $status ) ? $s : 'new';
        $data ['status'] = $s;
        
        if ($s == 'pass') {
            $I = whoami ();
            $data ['approved_uid'] = $I ['uid'];
            $data ['approved_time'] = time ();
        } else {
            $data ['approved_uid'] = 0;
            $data ['approved_time'] = 0;
        }
        $rst = $cmM->update ( $data, array ('id IN' => $ids ) );
    }
    if ($rst === false) {
        show_page_tip ( '<strong>Oops!</strong><br/>出错啦:' . db_error ( true ), 'error' );
    }
    Response::back ();
}