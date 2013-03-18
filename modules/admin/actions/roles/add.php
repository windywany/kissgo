<?php
assert_login ();
/**
 * 新增角色
 * @param Request $req
 * @param Response $res
 */
function do_admin_roles_add_get($req, $res) {
    imports ( 'admin/forms/RoleForm.php' );
    $data ['_CUR_URL'] = murl ( 'admin', 'roles' );
    $data ['form'] = new RoleForm ( true );
    return view ( 'admin/views/role/addrole.tpl', $data );
}