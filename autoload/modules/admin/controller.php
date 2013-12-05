<?php
class AdminController extends Controller {
    private $user;
    public function preRun() {
        $this->user = whoami ();
    }
    public function index_post() {
        $formid = sess_get ( 'formid' );
        $i = whoami ();
        $i->isLogin ( true );
        $i->save ();
        return new JsonView ( array ('success' => true, 'to' => ADMINCP_URL ) );
    }
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
}