<?php
/*
 * delete a user from a group
 * @author Leo Ning
*/
assert_login ();
function do_admin_users_dfg_get($req, $res) {    
    $uid = irqst ( 'uid' );
    $gid = irqst ( 'gid' );
    $where ['uid'] = $uid;
    $where ['rid'] = $gid;
    $guM = new KsgUserRoleTable ();
    if ($guM->remove ( $where ) !== false) {
        return new JsonView ( array ('success' => true ) );
    } else {
        return new JsonView ( array ('success' => false, 'msg' => PdoDialect::$last_error_message ) );
    }
}