<?php
/**
 * 退出(注销)
 * @param Request $req
 * @param Response $res
 * @author Leo Ning
 */
function do_admin_logout($req, $res) {
    session_destroy ();
    Response::redirect ( murl ( 'admin', 'login' ) );
}