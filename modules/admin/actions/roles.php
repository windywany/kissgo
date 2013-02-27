<?php
/*
 * 角色管理
 * 
 */
assert_login ();
imports ( 'admin/models/*' );
function do_admin_roles_get($req, $res) {
    $data = array ('limit' => 10 );
    $start = irqst ( 'start', 1 ); // 分页
    //$where = Model::where_build ( array ('id', 'label like', 'name like' ), $data, array ('label' => 'like', 'name' => 'like' ) );
    

    $rm = new CoreRoleTable ();
    
    $roles = $rm->query ()->limit ( $start, $data ['limit'] )->sort ( 'rid' );
    
    $data ['countTotal'] = count ( $roles );
    if ($data ['countTotal']) {
        $data ['items'] = $roles;
    } else {
        $data ['items'] = array ();
    }
    
    $data ['reserves'] = array (0 => '', '1' => '<span class="label">内置</span>' );
    $data ['_CUR_URL'] = murl ( 'admin', 'roles' );
    return view ( 'admin/views/user/roles.tpl', $data );
}
