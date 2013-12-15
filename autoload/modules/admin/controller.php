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
    public function index_post($formid) {
        $data = array ('success' => false );
        $_formid = sess_get ( 'formid' );
        if ($_formid != $formid) {
            $data ['msg'] = '非法表单';
        } else {
            $form = new LoginForm ();
            $formData = $form->valid ();
            if ($formData) {
                $where = new Condition ();
                if (strpos ( $formData ['username'], '@' )) {
                    $where ['email'] = $formData ['username'];
                    $id = 'email';
                } else {
                    $where ['username'] = $formData ['username'];
                    $id = 'username';
                }

                $user = dbselect ( '*' )->from ( '{users}' )->where ( $where );
                if (count ( $user ) == 0 || $user [0] ['passwd'] != md5 ( $formData ['passwd'] ) || $user [0] [$id] != $formData ['username']) {
                    $data ['msg'] = __ ( '@admin:Invalide User Name or Password' );
                } else if (empty ( $user [0] ['status'] )) {
                    $data ['msg'] = __ ( '@admin:User is locked!' );
                } else {
                    $user = $user [0];
                    $loginInfo = new LoginInfo ( $user ['id'], $user ['username'], $user ['display_name'], time (), $_SERVER ['REMOTE_ADDR'] );
                    $loginInfo->login ( true );
                    LoginInfo::save ( $loginInfo );
                    $data ['success'] = true;
                    $data ['to'] = ADMINCP_URL;
                }
            } else {
                $data ['msg'] = __ ( '@admin:Invalide User Name or Password' );
            }
        }
        return new JsonView ( $data );
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
                $form = new LoginForm ();
                $data ['form'] = $form;
                $data ['validateRule'] = $form->rules ();
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