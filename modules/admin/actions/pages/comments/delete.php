<?php
/*
 * 彻底删除评论
 */
assert_login ();
function do_admin_pages_comments_delete_get($req, $res) {
    $ids = rqst ( 'cid' );
    $ids = safe_ids ( $ids, ",", true );
    
    if (! empty ( $ids )) {
        $cmM = new NodeCommentTable ();
        $rst = $cmM->remove ( array ('id IN' => $ids ) );
        if ($rst) {
            show_page_tip ( '<strong>恭喜!</strong><br/>删除成功.' );
        } else {
            show_page_tip ( '<strong>Oops!</strong><br/>出错了：' . db_error ( true ), 'error' );
        }
    } else {
        show_page_tip ( '<strong>Oops!</strong><br/>出错了,无效的评论编号.', 'error' );
    }
    Response::back ();
}