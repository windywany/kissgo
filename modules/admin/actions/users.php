<?php
/*
 * 用户管理
 */
assert_login ();
/**
 * 用户列表
 * @param unknown_type $req
 * @param unknown_type $res
 * @return SmartyView
 */
function do_admin_users_get($req, $res) {
    imports ( 'admin/models/CoreRoleTable.php', 'admin/forms/UserForm.php', 'admin/models/CoreUserTable.php' );
    $data = array ('limit' => 10 );
    $start = irqst ( 'start', 1 ); // 分页
    new UserForm ( null );
    
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
    bind ( 'get_user_options', 'admin_get_user_options', 10, 2 );
    bind ( 'get_user_bench_options', 'admin_get_user_bench_options' );
    bind ( 'user_belongs', 'admin_user_belongs', 10, 2 );
    return view ( 'admin/views/user/users.tpl', $data );
}
/**
 * 保存用户数据
 * @param Request $req
 * @param Reponse $res
 */
function do_admin_users_post($req, $res) {
    imports ( 'admin/forms/UserForm.php', 'admin/models/CoreUserTable.php' );
    
    $form = new UserForm ();
    $msg = __ ( 'Sorry, some error occurred during validating user data.' );
    if ($req ['uid']) { //编辑时去除密码的required检验
        $pwdWd = $form->getWidget ( 'passwd' );
        $pwdWd->removeValidate ( 'required' );
    }
    if ($form->validate ()) {
        $next_op = $req ['nextOp'];
        
        $crt = new CoreUserTable ();
        $data = $form->getCleanData ();
        $where = array ();
        if ($data ['uid']) {
            $where ['uid'] = $data ['uid'];
        }
        unset ( $data ['uid'] );
        try {
            if (! empty ( $data ['passwd'] )) {
                $data ['passwd'] = md5 ( $data ['passwd'] );
            } else {
                unset ( $data ['passwd'] );
            }
            unset ( $data ['passwd1'] );
            
            $rst = $crt->save ( $data )->where ( $where );
            if (count ( $rst ) === false) {
                $msg = __ ( 'Sorry, some error occurred during saving user data.' );
            } else {
                show_page_tip ( __ ( 'Congratulations. The user has been saved successfully!' ), 'success' );
                if ($rst->isNew) {
                    $uid = $crt->lastId ();
                    $form->setValue ( 'uid', $uid );
                }
                if ('sc' == $next_op) {
                    $form->destroy ();
                    Response::redirect ( murl ( 'admin', 'users' ) );
                } else if ('sn' == $next_op) {
                    $form->destroy ();
                    Response::redirect ( murl ( 'admin', 'users/add' ) );
                } else {
                    $form->persist ();
                    Response::redirect ( murl ( 'admin', 'users/edit' ) );
                }
            }
        } catch ( PDOException $e ) {
            $msg = __ ( 'Sorry, some error occurred during saving user data: ' . $e->getMessage () );
        }
    }
    show_page_tip ( $msg, 'error' );
    $form->persist ();
    Response::back ( array ('__bk' => 1 ) );
}
function admin_get_user_options($options, $user) {
    static $url = false;
    if (! $url) {
        $url = murl ( 'admin', 'users' );
    }
    $options .= '<a title="编辑" href="' . $url . '/edit?uid=' . $user ['uid'] . '"><i class="icon-edit"></i></a>';
    if (! $user ['reserved']) {
        if ($user ['status'] == '1') {
            $options .= '<a title="禁用" onclick="return confirm(\'确定要禁用该用户账户?\');" href="' . $url . '/active?status=0&uid=' . $user ['uid'] . '"><i class="icon-ban-circle"></i></a>';
        } else {
            $options .= '<a title="激活" onclick="return confirm(\'确定要激活该用户账户?\');" href="' . $url . '/active?status=1&uid=' . $user ['uid'] . '"><i class="icon-ok-circle"></i></a>';
        }
    }
    if (! $user ['reserved'] && icando ( 'delete', 'user' )) {
        $options .= '<a title="删除" onclick="return confirm(\'确定要删除该用户账户?\');" href="' . $url . '/del?uid=' . $user ['uid'] . '"><i class="icon-trash"></i></a>';
    }
    if (icando ( 'grant', 'user' )) {
        $options .= '<a title="授权" data-content="' . $user ['name'] . '" class="grant-user" id="grant-USER-' . $user ['uid'] . '" href="' . ADMIN_URL . 'dashboard/?Ctlr=Grant&type=USER&uid=' . $user ['uid'] . '"><i class="icon-user"></i></a>';
    }
    return $options;
}
function admin_get_user_bench_options($options) {
    static $url = false;
    if (! $url) {
        $url = murl ( 'admin', 'users' );
    }
    if (icando ( 'delete', 'user' )) {
        $options .= '<li><a href="' . $url . '/del" id="menu-del-user"><i class="icon-trash"></i>删除</a></li>';
    }
    $options .= '<li><a href="' . $url . '/active?status=1" class="menu-active-user"><i class="icon-ok-circle"></i>激活</a></li>';
    $options .= '<li><a href="' . $url . '/active?status=0" class="menu-active-user"><i class="icon-ban-circle"></i>禁用</a></li>';
    return $options;
}
function admin_user_belongs($lists, $user) {
    static $guM = null;
    if ($guM == null) {
        imports ( 'admin/models/CoreUserRoleTable.php' );
        $guM = new CoreUserRoleTable ();
    }
    $uid = $user ['uid'];
    $groups = $guM->getGroups ( $uid );
    if ($groups) {
        foreach ( $groups as $g ) {
            $cls = 'label-info';
            if (empty ( $g ['name'] )) {
                $g ['name'] = '已删除的组';
                $cls = 'label-warning';
            }
            $lists .= "<span class=\"label {$cls} mg-r5\">{$g['name']}<a href=\"#{$uid}/{$g['rid']}\" class=\"delete-from-group\"><i title=\"点击将用户从该组移除.\" class=\"icon-remove-sign icon-white\"></i></a></span>";
        }
    }
    return $lists;
}