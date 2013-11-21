<?php
class AdminController extends Controller {
    private $user;
    public function preRun() {
        $this->user = whoami ();
    }
    
    public function index($abc, $def = 12) {
        $data ['siteurl'] = BASE_URL;
        $data ['moduledir'] = MODULE_DIR;
        if ($this->user->isLogin ()) {
            return view ( 'index.tpl', $data );
        } else {
            return view ( 'login.tpl', $data );
        }
    }
    public function login() {
        if (! $this->user->isLogin ()) {

        }
        Response::redirect ( ADMINCP_URL );
    }
}