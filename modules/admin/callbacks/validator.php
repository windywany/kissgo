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
/**
 * 
 * @param string $value
 * @param array $data
 * @param string $message
 */
function check_node_url($value = null, $data = null, $message = '') {
    $type = $data ['node_type'];
    if (empty ( $value )) {
        if ($type == 'catalog') {
            return '请输入合法的虚拟路径.';
        }
        return true;
    }
    $reg = '/^(https?:\/{2})?.+/';
    if ($type == 'catalog') {
        $reg = '/^[\d\w][\d\w_-]*\/?$/';
    }
    return preg_match ( $reg, $value ) ? true : $message;
}
//end of validator_callbacks.php