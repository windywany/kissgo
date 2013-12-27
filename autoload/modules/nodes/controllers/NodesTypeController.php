<?php

/**
 *
 * Node type controller
 * @author guangfeng.ning
 *
 */
class NodesTypeController extends Controller {

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }

    public function index() {
        $ctm = ContentTypeManager::getInstance ();
        $data ['types'] = $ctm->getTypes ();
        $data ['totalType'] = count ( $data ['types'] );
        return view ( 'types.tpl', $data );
    }
}