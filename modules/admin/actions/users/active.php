<?php
/*
 * 激活或禁用用户
 * @author Leo
 * @date 2013-03-11 21:36
 */
assert_login ();
function do_admin_users_active_get($req, $res) {    
    if ($req ['uids']) {
        $where ['uid IN'] = safe_ids ( rqst ( 'uids' ), ',', true );
    } else {
        $where ['uid'] = irqst ( 'uid' );
    }
    $status = irqst ( 'status', 1 );
    $status = $status == 1 ? 1 : 0;
    if ($status == 0) {
        $where ['reserved'] = 0;
    }
    $userModel = new KsgUserTable ();
    $rst = $userModel->save ( array ('status' => $status ) )->where ( $where );
    $msg = $status ? '激话' : '禁用';
    try {
        if (count ( $rst ) >= 0) {
            show_page_tip ( $msg . '用户账户成功.', 'success' );
        } else {
            $message = $msg . '失败.';
        }
    } catch ( PDOException $e ) {
        $message = $msg . '失败:' . $e->getMessage ();
    }
    $res->back ();
}