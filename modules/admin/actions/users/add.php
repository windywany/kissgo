<?php
assert_login ();
/**
 * 
 * @param Request $req
 * @param Response $res
 * @return SmartyView
 */
function do_admin_users_add_get($req, $res) {
    imports ( 'admin/forms/UserForm.php' );
    
    $data ['_CUR_URL'] = murl ( 'admin', 'users' );
    
    $data ['form'] = new UserForm (true);
    return view ( 'admin/views/user/adduser.tpl', $data );
}
