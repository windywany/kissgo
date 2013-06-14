<?php
/*
 * 角色管理
 * 
 */
assert_login ();

/**
 * 
 * @param Request $req
 * @param Response $res
 * @return SmartyView
 */
function do_admin_roles_get($req, $res) {    
    $data = array ('limit' => 10 );
    $start = irqst ( 'start', 1 ); // 分页
    
    new RoleForm ();
    
    $where = where ( array ('label' => array ('like' => array ('prefix' => '%', 'suffix' => '%' ) ) ), $data );
    
    $where += where ( array ('name' => array ('like' => array ('prefix' => '%', 'suffix' => '%' ) ) ), $data );
    
    $rm = new KsgRoleTable ();
    $roles = $rm->query ()->where ( $where )->limit ( $start, $data ['limit'] )->sort ( 'rid', 'd' );
    
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
    $form = new RoleForm ();
    $msg = __ ( 'Sorry, some error occurred during validating role data.' );
    if ($form->validate ()) {
        $next_op = $req ['nextOp'];
        
        $crt = new KsgRoleTable ();
        $data = $form->getCleanData ();
        $where = array ();
        if ($data ['rid']) {
            $where ['rid'] = $data ['rid'];
        }
        unset ( $data ['rid'] );
        try {
            $rst = $crt->save ( $data )->where ( $where );
            if (count ( $rst ) === false) {
                $msg = __ ( 'Sorry, some error occurred during saving role data.' );
            } else {
                show_page_tip ( __ ( 'Congratulations. The role has been saved successfully!' ), 'success' );
                if ($rst->isNew) {
                    $rid = $crt->lastId ();
                    $form->setValue ( 'rid', $rid );
                }
                if ('sc' == $next_op) {
                    $form->destroy ();
                    Response::redirect ( murl ( 'admin', 'roles' ) );
                } else if ('sn' == $next_op) {
                    $form->destroy ();
                    Response::redirect ( murl ( 'admin', 'roles/add' ) );
                } else {
                    $form->persist ();
                    Response::redirect ( murl ( 'admin', 'roles/edit' ) );
                }
            }
        } catch ( PDOException $e ) {
            $msg = __ ( 'Sorry, some error occurred during saving role data: ' . $e->getMessage () );
        }
    }
    show_page_tip ( $msg, 'error' );
    $form->persist ();
    Response::back ( array ('__bk' => 1 ) );
}