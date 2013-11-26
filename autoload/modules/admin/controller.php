<?php
class AdminController extends Controller {
    private $user;
    public function preRun() {
        $this->user = whoami ();
    }
    public function index($abc, $def = 12) {
        if (Request::isAjaxRequest () && Request::isPost ()) {
            return $this->login ();
        } else {
            $data ['siteurl'] = BASE_URL;
            $data ['moduledir'] = MODULE_DIR;
            $data ['admincp'] = ADMINCP_URL;
            if ($this->user->isLogin ()) {
                return view ( 'index.tpl', $data );
            } else {
                $formid = randstr ( 8 );
                $_SESSION ['formid'] = $formid;
                $data ['formid'] = $formid;
                return view ( 'login.tpl', $data );
            }
        }
    }
    private function login() {
        $formid = sess_get ( 'formid' );
        $i = whoami ();
        $i->isLogin ( true );
        $i->save ();
        return new JsonView ( array ('success' => true, 'to' => ADMINCP_URL ) );
    }
}