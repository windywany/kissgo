<?php
/*
 * 用户管理
 */
assert_login ();
imports ( 'admin/models/*' );
function do_admin_users_get($req, $res) {
    $data = array ('limit' => 10 );
    $start = irqst ( 'start', 1 ); // 分页
    

    $userModel = new CoreUserTable ();
    
    $users = $userModel->query ( 'U.*', 'U' );
    
    $where = array ('deleted' => 0 );
    
    $where += where ( array ('U.login' => array ('like' => array ('name' => 'login', 'prefix' => '%', 'suffix' => '%' ) ) ), $data );
    
    $where += where ( array ('U.email' => array ('like' => array ('name' => 'email', 'prefix' => '%', 'suffix' => '%' ) ) ), $data );
    
    $where += where ( array ('U.status' => 'status' ), $data );
    
    $where += where ( array ('UR.rid' => 'rid' ), $data );
    
    if (isset ( $where ['UR.rid'] )) {
        $users->ljoin ( 'user_role AS UR', 'U.uid=UR.uid' );
    }
    
    $users = $users->where ( $where )->limit ( $start, $data ['limit'] )->sort ();
    
    $data ['totalUser'] = count ( $users );
    
    if ($data ['totalUser']) {
        $data ['users'] = $users;
    }
    
    $data ['stas'] = array (1 => '<span class="label label-success">正常</span>', '0' => '<span class="label">禁用</span>' );
    $gM = new CoreRoleTable ();
    $roles = $gM->query ()->where ( array ('deleted' => 0 ) );
    $data ['roles'] = $roles;
    
    if (count ( $roles )) {
        $data ['role_options'] = $roles->toArray ( 'rid', 'name', array ('' => '请选择角色' ) );
    } else {
        $data ['role_options'] = array ('0' => '请选择角色' );
    }
    $data ['_CUR_URL'] = murl ( 'admin', 'users' );
    return view ( 'admin/views/user/users.tpl', $data );
}
function do_admin_users_post($req, $res) {
    return do_admin_users_get ( $req, $res );
}