<?php
/*
 * 角色管理
 * 
 */
assert_login ();
imports ( 'admin/models/*' );
/**
 * 
 * @param Request $req
 * @param Response $res
 * @return SmartyView
 */
function do_admin_roles_get($req, $res) {
    $data = array ('limit' => 10 );
    $start = irqst ( 'start', 1 ); // 分页
    $where = where ( array ('label' => array ('like' => array ('prefix' => '%', 'suffix' => '%' ) ) ), $data );
    $where += where ( array ('name' => array ('like' => array ('prefix' => '%', 'suffix' => '%' ) ) ), $data );
    
    $rm = new CoreRoleTable ();
    $roles = $rm->query ()->where ( $where )->limit ( $start, $data ['limit'] )->sort ();
    
    $data ['countTotal'] = count ( $roles );
    if ($data ['countTotal']) {
        $data ['items'] = $roles;
    } else {
        $data ['items'] = array ();
    }
    
    $data ['reserves'] = array (0 => '', '1' => '<span class="label">内置</span>' );
    $data ['_CUR_URL'] = murl ( 'admin', 'roles' );
    return view ( 'admin/views/role/roles.tpl', $data );
}
/**
 * 
 * @param Request $req
 * @param Response $res
 */
function do_admin_roles_post($req, $res) {
    imports ( 'admin/forms/RoleForm.php' );
    $form = new RoleForm ();
    if ($form->validate ()) {
        show_page_tip ( __ ( 'Congratulations, the role data have been saved successfully!' ), 'success' );
        
        return view ( 'admin/views/role/roles.tpl', array () );
    } else {
        show_page_tip ( __ ( 'Sorry, some error occurred when saving role data.' ), 'error' );
        $form->persist ();
        Response::back ();
    }
}