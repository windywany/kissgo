<?php
assert_login ();
/**
 * 
 * @param Request $req
 * @param Response $res
 * @return SmartyView
 */
function do_admin_users_edit_get($req, $res) {    
    $data ['_CUR_URL'] = murl ( 'admin', 'users' );
    $uid = 0;
    if (isset ( $req ['uid'] )) {
        $uid = $req ['uid'];
    }
    $user_data = true;
    if ($uid && ! isset ( $req ['__bk'] )) {
        $crt = new KsgUserTable ();
        $user_data = $crt->query ()->where ( array ('uid' => $uid ) );
        try {
            $user_data = $user_data [0];
        } catch ( PDOException $e ) {
            show_error_message ( $e->getMessage (), $data ['_CUR_URL'] );
        }
    }
    $uf = new UserForm ( $user_data );
    $pwdWidget = $uf->getWidget ( 'passwd' );
    $pwdWidget->removeValidate ( 'required' );
    $pwdWidget->setTip ( '如何不需要修改密码,请留空.' );
    $data ['form'] = $uf;
    return view ( 'admin/views/user/edituser.tpl', $data );
}