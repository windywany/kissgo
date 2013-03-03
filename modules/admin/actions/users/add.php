<?php
/**
 * 
 * @param unknown_type $req
 * @param unknown_type $res
 * @return SmartyView
 */
function do_admin_users_add_get($req, $res) {
    imports ( 'admin/forms/UserForm.php' );
    return view ( 'admin/views/user/adduser.tpl', array ('form' => new UserForm () ) );
}
