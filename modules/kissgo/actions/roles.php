<?php
/*
 * 角色管理
 * 
 */
assert_login ();
imports ( 'kissgo/models/*' );

$req = Request::getInstance ();

$data = array ('limit' => 10 );
$start = rqst ( 'start', 1 ); // 分页
$where = Model::where_build ( array ('id', 'label like', 'name like' ), $data, array ('label' => 'like', 'name' => 'like' ) );

$rm = new RoleEntity ();

$rm->sort ( array ('id', 'd' ) );

$roles = $rm->where ( $where )->limit ( $this->limit, $start - 1 )->count ( true )->retrieve ();

if ($roles) {
    $data ['items'] = $roles;
    $data ['countTotal'] = $roles->countTotal;
}
$data ['reserves'] = array (0 => '', '1' => '<span class="label">内置</span>' );
//$data ['group_types'] = apply_filter ( 'get_group_types', array ('' => '-请选择组类别-' ) );
//bind ( 'get_group_options', array ($this,'get_options' ), 10, 2 );
//bind ( 'get_group_bench_options', array ($this,'get_bench_options' ) );
return admin_view ( 'kissgo/user/roles.tpl', $data );