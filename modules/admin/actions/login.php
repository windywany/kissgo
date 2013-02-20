<?php
/**
 * 登录
 * User: Leo
 * Date: 12-11-24
 * Time: 下午1:01
 */
imports ( 'admin/forms/*' );
/**
 * 处理登录
 * @param Request $req
 * @param Response $res
 * @return ThemeView
 */
function do_admin_login_post($req, $res) {
    $form = new PassportForm ();
    if ($form->validate ()) {
        imports ( 'admin/models/*' );
        $um = new CoreUserTable ();
        $account = $form ['account'];
        $passwod = $form ['passwd'];
        $tryCount = sess_get ( 'login_try_count', 0 );
        $where ['deleted'] = 0;
        $id = 'login';
        if (strpos ( $account, '@' )) {
            $where ['email'] = $account;
            $id = 'email';
        } else {
            $where ['login'] = $account;
        }
        $user = $um->query ()->where ( $where );
        
        if (count ( $user ) == 0 || $user ['passwd'] != md5 ( $form ['passwd'] ) || $user [$id] != $account) {
            $form->setError ( 'account', '用户名或密码错误.' );
        } else if (empty ( $user ['status'] )) {
            $form->setError ( 'account', '用户已锁定，请联系管理员.' );
        } else {
            $loginInfo = new LoginInfo ( $user ['uid'], $user ['login'], time (), $_SERVER ['REMOTE_ADDR'] );
            $loginInfo->login ( true );
            LoginInfo::save ( $loginInfo );
            Response::redirect ( sess_del ( 'go_to_the_page_when_login', murl ( 'admin' ) ) );
        }
    }
    return template ( 'admin/login.tpl', array ('form' => $form ) );
}
/**
 * 显示登录页
 * @param Request $req
 * @param Response $res
 * @return ThemeView
 */
function do_admin_login_get($req, $res) {
    $me = Passport::getPassport ();
    if ($me->isLogin ()) {
        Response::redirect ( sess_get ( 'go_to_the_page_when_login', BASE_URL ) );
    }
    if (! empty ( $req ['from'] )) {
        $_SESSION ['go_to_the_page_when_login'] = $req ['from'];
    }
    $form = new PassportForm ();
    return template ( 'admin/login.tpl', array ('form' => $form ) );
}