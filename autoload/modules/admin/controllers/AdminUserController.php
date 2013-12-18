<?php

class AdminUserController extends Controller {

    public function index() {
        $data = array ();
        $view = view ( 'users.tpl', $data );
        return $view;
    }

    public function add() {

        $data ['action'] = __ ( '@admin:Add New User' );
        $form = new UserForm ();
        $data ['validateRule'] = $form->rules ();
        $data ['groups'] = $form->getBindData ( 'gid' );
        $view = view ( 'user_form.tpl', $data );
        return $view;
    }

    public function edit($id) {
        $data ['action'] = __ ( '@admin:Edit User' );
        $form = new UserForm ();
        $form->removeRlue ( 'password', 'required' );
        $data ['validateRule'] = $form->rules ();
        $view = view ( 'user_form.tpl', $data );
        return $view;
    }
    public function save(){

        return new JsonView(array());
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
        $users = dbselect ( 'U.*', 'G.name AS groupname' )->from ( '{users} AS U' )->join ( '{groups} AS G', 'U.gid = G.gid' )->limit ( $start, $rp );
        $total = $users->count ( 'U.id' );
        $jsonData = array ('page' => $page, 'total' => $total, 'rows' => array (), 'rp' => $rp );
        if ($total > 0 && count ( $users )) {
            foreach ( $users as $u ) { // the order is very important
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