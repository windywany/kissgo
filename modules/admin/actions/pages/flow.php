<?php
/*
 * node 流程控制
 */
assert_login ();
function do_admin_pages_flow_get($req, $res) {
    $status = array ('draft', 'published', 'approving', 'approved', 'unapproved', 'published' );
    $ids = rqst ( 'pid' );
    $ids = safe_ids ( $ids, ",", true );
    if (empty ( $ids )) {
        show_page_tip ( '<strong>Oops!</strong><br/>错误的页面编号', 'error' );
        Response::back ();
    }
    $nM = new KsgNodeTable ();
    if (isset ( $req ['del'] )) {
        $del = irqst ( 'del' );
        $deleted = $del ? 1 : 0;
        $rst = $nM->update ( array ('deleted' => $deleted ), array ('nid IN' => $ids ) );
    } else {
        $s = rqst ( 's', 'draft' );
        $s = in_array ( $s, $status ) ? $s : 'draft';
        $data ['status'] = $s;
        if ($s == 'published') {
            $data ['publish_time'] = time ();
        } else {
            $data ['publish_time'] = 0;
        }
        $rst = $nM->update ( $data, array ('nid IN' => $ids ) );
    }
    if ($rst === false) {
        show_page_tip ( '<strong>Oops!</strong><br/>出错啦:' . db_error ( true ), 'error' );
    }
    Response::back ();
}