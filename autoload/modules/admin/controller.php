<?php

/**
 *
 * Administrator Dashboard Controller
 * @author guangfeng.ning
 *
 */
class AdminController extends Controller {
    private $user;

    public function preRun() {
        $this->user = whoami ();
    }

    /**
     * login
     */
    public function index_post($formid, $name, $password) {
        $data = array ('success' => false );
        $_formid = sess_get ( 'formid' );
        if ($_formid != $formid) {
            $data ['msg'] = '非法表单';
        } else {
            $i = whoami ();
            $i->isLogin ( true );
            $i->save ();
        }
        return new JsonView ( array ('success' => true, 'to' => ADMINCP_URL ) );
    }

    /**
     * start page
     */
    public function index() {
        if (isset ( $this->request ['logout'] )) {
            LoginInfo::destroy ();
            Response::redirect ( ADMINCP_URL );
        } else if (isset ( $this->request ['clear'] )) {
            InnerCacher::clear ();
            Response::redirect ( ADMINCP_URL );
        } else {
            if ($this->user->isLogin ()) {
                $data ['styles'] = apply_filter ( 'get_admincp_style_files', array () );
                return view ( 'index.tpl', $data );
            } else {
                $formid = randstr ( 8 );
                $_SESSION ['formid'] = $formid;
                $data ['formid'] = $formid;
                return view ( 'login.tpl', $data );
            }
        }
    }

    /**
     * user list page
     */
    public function users() {
        $data = array ();
        return view ( 'users.tpl', $data );
    }

    /**
     * user data - JSON
     *
     * @param int $page
     * @param int $rp
     */
    public function users_data($page = 1, $rp = 15, $sortname = 'id', $sortorder = 'desc') {
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
                $cell [] = $u ['status'] ? '' : 'blocked';
                $cell [] = $u ['last_ip'];
                $cell [] = $u ['last_time'];
                $jsonData ['rows'] [] = array ('id' => $u ['id'], 'cell' => $cell );
            }
        }
        return new JsonView ( $jsonData );
    }
}