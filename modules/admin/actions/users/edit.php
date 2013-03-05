<?php
assert_login ();
/**
 * 
 * @param Request $req
 * @param Response $res
 * @return SmartyView
 */
function do_admin_users_edit_get($req, $res) {
    imports ( 'admin/forms/UserForm.php' );
    
    $data ['_CUR_URL'] = murl ( 'admin', 'users' );
    
    $data ['form'] = new UserForm ();
    return view ( 'admin/views/user/edituser.tpl', $data );
}