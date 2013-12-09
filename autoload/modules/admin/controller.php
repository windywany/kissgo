<?php
class AdminController extends Controller {
    private $user;
    public function preRun() {
        $this->user = whoami ();
    }
    /**
     * login
     */
    public function index_post() {
        $formid = sess_get ( 'formid' );
        $i = whoami ();
        $i->isLogin ( true );
        $i->save ();
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
    public function users_data($page = 1, $rp = 10, $sortname = 'id', $sortorder = 'desc') {
        $jsonData = array ('page' => $page, 'total' => 1, 'rows' => array (), 'rp' => $rp );
        $jsonData ['rows'] = array (array ('id' => 1, 'cell' => array (1 ) ) );
        return new JsonView ( $jsonData );
    }
}