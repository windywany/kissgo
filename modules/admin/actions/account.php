<?php
/**
 * 用户面板
 */
assert_login ();
function do_admin_account_get($req, $res) {
    $type = rqst ( 'group', 'base' );
    bind ( 'get_account_base_tpl', 'get_account_base_tpl' );
    $data ['_CUR_URL'] = murl ( 'admin', 'account' );
    $data ['__group'] = rqst ( 'group', 'base' );
    $data ['__opt_groups'] = apply_filter ( 'get_account_group', array ('base' => '基本资料' ) );
    $data ['__account_tpl'] = apply_filter ( "get_account_{$type}_tpl", false );
    
    if ($data ['__account_tpl'] instanceof BaseForm) {
        $data ['__account_form'] = $data ['__account_tpl'];
        unset ( $data ['__account_tpl'] );
    }
    return view ( 'admin/views/account.tpl', $data );
}
/**
 * 保存
 */
function do_admin_account_post($req, $res) {
    $type = $req ['__group'];
    bind ( 'on_save_account_base', 'save_account_base' );
    $rst = apply_filter ( "on_save_account_{$type}", false );
    if ($rst) {
        Response::redirect ( murl ( 'admin', 'account' ) );
    } else {
        Response::back ();
    }
}
/**
 * base form
 */
function get_account_base_tpl($form) {
    if ($form) {
        return $form;
    }
    if (Response::isBack ()) {
        return new AccountForm ( true );
    } else {
        $me = whoami ();
        $uid = $me ['uid'];
        $userModel = new CoreUserTable ();
        $user = $userModel->read ( array ('uid' => $uid ) );
        if ($user == false) {
            show_page_tip ( "<strong>出错啦!</strong>uid为{$uid}的用户不存在。", 'error' );
        } else {
            return new AccountForm ( $user );
        }
    }
}
/**
 * 保存基本数据
 * @param boolean $rtn
 * @return boolean
 */
function save_account_base($rtn) {
    $form = new AccountForm ();
    if ($form->valid ()) { //用户数据合法
        $user = $form->getCleanData ();
        if (empty ( $user ['passwd'] )) {
            unset ( $user ['passwd'] );
        }
        unset ( $user ['passwd1'] );
        if (isset ( $user ['passwd'] )) {
            $user ['passwd'] = md5 ( $user ['passwd'] );
        }
        $where ['uid'] = $user ['uid'];
        unset ( $user ['uid'] );
        $userModel = new CoreUserTable ();
        $rst = $userModel->save ( $user )->where ( $where );
        if (count ( $rst ) == 1) {
            show_page_tip ( '<strong>恭喜,</strong>用户账户修改成功.' );
            return true;
        } else {
            $form->persist ();
            show_page_tip ( '<strong>出错啦!</strong>' . db_error (), 'error' );
        }
    } else {
        $form->persist ();
    }
    return false;
}