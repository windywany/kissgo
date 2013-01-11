<?php
/*
 * 用户管理
 */
assert_login ();
imports ( 'kissgo/models/*' );

$data = array ('limit' => 10 );
$start = rqst ( 'start', 1 ); // 分页

$where = Model::where_build ( array ('uid as U.uid', 'account like', 'name like', 'email like', 'status' => 0 ), $data, array ('account' => 'like', 'email' => 'like', 'name' => 'like' ) );

$data ['rid'] = irqst ( 'rid' );
$userModel = new UserEntity ();
$userModel->alias ( 'U' );
if (! empty ( $data ['rid'] ) && is_numeric ( $data ['rid'] )) {
    $userModel->join ( 'core_groupuser AS CG', "U.uid = CG.uid", Model::INNER );
    $where ['CG.gid'] = $data ['rid'];
}
$userModel->sort ( array ('uid', 'd' ) );
$users = $userModel->where ( $where )->limit ( $this->limit, $start - 1 )->count ( true )->retrieve ( "U.*" );
if ($users) {
    $data ['users'] = $users;
    $data ['totalUser'] = $users->countTotal;
}

$data ['stas'] = array (0 => '<span class="label label-success">正常</span>', '1' => '<span class="label">禁用</span>' );
$gM = new RoleEntity ();
$roles = $gM->select ();
$data ['roles'] = $roles;

if ($roles) {
    $data ['role_options'] = $roles->toArray ( 'id', 'name', array ('0' => '请选择角色' ) );
} else {
    $data ['role_options'] = array ('0' => '请选择角色' );
}
//bind ( 'get_user_options', array ($this, 'get_user_options' ), 10, 2 );


//bind ( 'get_user_bench_options', array ($this, 'get_user_bench_options' ) );


//bind ( 'user_belongs', array ($this, 'user_belongs' ), 10, 2 );


return admin_view ( 'kissgo/user/users.tpl', $data );