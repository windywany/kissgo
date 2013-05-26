<?php
/*
 * add user to groups
 */
assert_login ();
function do_admin_users_a2g_get($req, $res) {    
    $uid = irqst ( 'uid' );
    $gids = safe_ids ( rqst ( 'gids' ), ',', true );
    $data ['success'] = true;
    if (empty ( $uid ) || empty ( $gids )) {
        $data ['success'] = false;
        $data ['msg'] = '用户编号或组编号为空,操作无法进行。';
    } else {
        $guM = new CoreUserRoleTable ();
        $rst = $guM->addToGroup ( $uid, $gids );
        if (! $rst) {
            $data ['success'] = false;
            $data ['msg'] = '数据库操作失败：' . PdoDriver::$last_error_message;
        }
    }
    return new JsonView ( $data );
}