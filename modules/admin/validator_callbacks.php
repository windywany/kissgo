<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 检验角色名是否已经存在
 * @param string|null $value
 * @param array|Request $data
 * @param string|null $message
 * @return
 */
function check_role_name($value = null, $data = null, $message = null) {
    $where ['deleted'] = 0;
    $where ['label'] = $data ['label'];
    if ($data ['rid']) {
        $where ['rid <>'] = $data ['rid'];
    }
    $crt = new KsgRoleTable ();
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
    $where ['deleted'] = 0;
    $where ['login'] = $data ['login'];
    if ($data ['uid']) {
        $where ['uid <>'] = $data ['uid'];
    }
    $crt = new KsgUserTable ();
    $rst = $crt->query ()->where ( $where );
    return count ( $rst ) ? false : true;
}
/**
 * 检验用户邮箱是否已经存在
 * @param string|null $value
 * @param array|Request $data
 * @param string|null $message
 * @return
 */
function check_user_email($value = null, $data = null, $message = '') {
    $where ['deleted'] = 0;
    $where ['email'] = $data ['email'];
    if ($data ['uid']) {
        $where ['uid <>'] = $data ['uid'];
    }
    $crt = new KsgUserTable ();
    $rst = $crt->query ()->where ( $where );
    try {
        return count ( $rst ) ? false : true;
    } catch ( PDOException $e ) {}
    return false;
}
/**
 * 检测菜单名是否重复
 * @param string|null $value
 * @param array|Request $data
 * @param string|null $message
 * @return Ambigous <string, boolean>
 */
function check_menu_name($value = null, $data = null, $message = '') {
    $where ['menu_name'] = $value;
    if (isset ( $data ['menu_id'] ) && ! empty ( $data ['menu_id'] )) {
        $where ['menu_id != '] = $data ['menu_id'];
    }
    $nM = new KsgMenuTable ();
    return $nM->exist ( $where ) ? $message : true;
}