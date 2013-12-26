<?php

/**
 *
 * user controller
 * @author guangfeng.ning
 *
 */
class AdminUserController extends Controller {

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }

    /**
     *
     * 用户列表页
     */
    public function index() {
        $data = array ();
        $form = new UserForm ();
        $data ['groups'] = $form->groups ( array ('' => '全部' ) );
        $view = view ( 'users.tpl', $data );
        return $view;
    }

    /**
     *
     * 新增页面
     */
    public function add() {
        $data ['action'] = __ ( '@admin:Add New User' );
        $form = new UserForm ();
        $data ['validateRule'] = $form->rules ();
        $data ['groups'] = $form->groups ();
        $view = view ( 'user_form.tpl', $data );
        return $view;
    }

    /**
     *
     * 编辑页面
     * @param int $id
     */
    public function edit($id) {
        $data ['action'] = __ ( '@admin:Edit User' );
        $user = dbselect ( '*' )->from ( '{users}' )->where ( array ('id' => intval ( $id ) ) );
        if (count ( $user )) {
            $user = $user [0];
        } else {
            $user = array ();
        }
        $form = new UserForm ( $user );
        $form->removeRlue ( 'password', 'required' );
        $data ['validateRule'] = $form->rules ();
        $data ['groups'] = $form->getBindData ( 'gid' );
        $data ['user'] = $user;
        $view = view ( 'user_form.tpl', $data );
        return $view;
    }

    /**
     *
     * 保存用户数据
     * @param int $id
     */
    public function save($id = 0) {
        $data = array ('success' => false );
        $form = new UserForm ();
        if (! empty ( $id )) {
            $form->removeRlue ( 'password', 'required' );
        }
        if ($form->valid ()) {
            $id = $form->save ( $id );
            if ($id) {
                $data ['success'] = true;
                $data ['id'] = $id;
            } else {
                $data ['msg'] = '无法保存用户信息.';
            }
        } else {
            $formerr = $form->getErrors ();
            $data ['formerr'] = $formerr;
        }
        return new JsonView ( $data );
    }

    /**
     * user data - JSON
     *
     * @param int $page
     * @param int $rp
     */
    public function data($page = 1, $rp = 15, $sortname = 'id', $sortorder = 'desc') {
        $page = intval ( $page );
        $rp = intval ( $rp );
        $rp = $rp ? $rp : 15;
        $start = ($page ? $page - 1 : $page) * $rp;
        $where = Condition::where ( array ('display_name', 'like' ), 'status', array ('username', 'like' ), array ('email', 'like' ), 'G.gid' );
        $users = dbselect ( 'U.*', 'G.name AS groupname' )->from ( '{users} AS U' )->join ( '{groups} AS G', 'U.gid = G.gid' )->where ( $where )->limit ( $start, $rp )->sort ( $sortname, $sortorder );
        $total = $users->count ( 'U.id' );
        $jsonData = array ('page' => $page, 'total' => $total, 'rows' => array (), 'rp' => $rp );
        if ($total > 0 && count ( $users )) {
            foreach ( $users as $u ) {
                // the order is very important
                $cell = array ();
                $cell [] = $u ['id'];
                $cell [] = $u ['username'];
                $cell [] = $u ['display_name'];
                $cell [] = $u ['groupname'];
                $cell [] = $u ['email'];
                $cell [] = $u ['status'] ? '' : __ ( '@admin:blocked' );
                $cell [] = $u ['last_ip'];
                $cell [] = $u ['last_time'];
                $jsonData ['rows'] [] = array ('id' => $u ['id'], 'cell' => $cell );
            }
        }
        return new JsonView ( $jsonData );
    }
}