<?php
/**
 * 退出(注销)
 * @param Request $req
 * @param Response $res
 * @author Leo Ning
 */
function do_passport_logout($req, $res) {
    session_destroy ();
    Response::redirect ( murl ( 'passport', 'login' ) );
}