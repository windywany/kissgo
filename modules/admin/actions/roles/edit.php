<?php
assert_login ();
/**
 * edit a role by giving a the id of it or get the role data from session
 * @param Request $req
 * @param Response $res
 */
function do_admin_roles_edit_get($req, $res) {    
    $data ['_CUR_URL'] = murl ( 'admin', 'roles' );
    $rid = 0;
    if (isset ( $req ['rid'] )) {
        $rid = $req ['rid'];
    }
    $role_data = true;
    if ($rid && ! isset ( $req ['__bk'] )) {
        $crt = new CoreRoleTable ();
        $role_data = $crt->query ()->where ( array ('rid' => $rid ) );
        try {
            $role_data = $role_data [0];
        } catch ( PDOException $e ) {
            show_error_message ( $e->getMessage (), $data ['_CUR_URL'] );
        }
    }
    $data ['form'] = new RoleForm ( $role_data );
    return view ( 'admin/views/role/editrole.tpl', $data );
}