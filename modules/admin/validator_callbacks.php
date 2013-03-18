<?php
/**
 * 检验角色名是否已经存在
 * @param string|null $value
 * @param array|Request $data
 * @param string|null $message
 * @return
 */
function check_role_name($value = null, $data = null, $message = null) {
    imports ( 'admin/models/CoreRoleTable.php' );
    $where ['deleted'] = 0;
    $where ['label'] = $data ['label'];
    if ($data ['rid']) {
        $where ['rid <>'] = $data ['rid'];
    }
    $crt = new CoreRoleTable ();
    $rst = $crt->query ()->where ( $where );
    try {
        return count ( $rst ) ? false : true;
    } catch ( PDOException $e ) {}
    return false;
}
/**
 * 检验用户账户名是否已经存在
 * @param string|null $value
 * @param array|Request $data
 * @param string|null $message
 * @return
 */
function check_user_login($value = null, $data = null, $message = '') {
    imports ( 'admin/models/CoreUserTable.php' );
    $where ['deleted'] = 0;
    $where ['login'] = $data ['login'];
    if ($data ['uid']) {
        $where ['uid <>'] = $data ['uid'];
    }
    $crt = new CoreUserTable ();
    $rst = $crt->query ()->where ( $where );
    try {
        return count ( $rst ) ? false : true;
    } catch ( PDOException $e ) {}
    return false;
}
/**
 * 检验用户邮箱是否已经存在
 * @param string|null $value
 * @param array|Request $data
 * @param string|null $message
 * @return
 */
function check_user_email($value = null, $data = null, $message = '') {
    imports ( 'admin/models/CoreUserTable.php' );
    $where ['deleted'] = 0;
    $where ['email'] = $data ['email'];
    if ($data ['uid']) {
        $where ['uid <>'] = $data ['uid'];
    }
    $crt = new CoreUserTable ();
    $rst = $crt->query ()->where ( $where );
    try {
        return count ( $rst ) ? false : true;
    } catch ( PDOException $e ) {}
    return false;
}