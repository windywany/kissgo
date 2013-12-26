<?php

class NodesTypeController extends Controller {

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }
    public function index(){

    }
}