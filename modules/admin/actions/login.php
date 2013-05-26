<?php
/**
 * 登录
 * User: Leo
 * Date: 12-11-24
 * Time: 下午1:01
 */
/**
 * 处理登录
 * @param Request $req
 * @param Response $res
 * @return ThemeView
 */
function do_admin_login_post($req, $res) {
    $form = new PassportForm ();
    $tryCount = sess_get ( 'login_try_count', 0 );
    if ($form->validate ()) {
        $goon = true;
        if (sess_get ( 'login_need_captcha', false )) {
            $code = strtolower ( $req ['captcha'] );
            $code1 = sess_get ( '__CAPTCHA__', false );
            $timeout = sess_get ( '__CAPTCHA__TIMEOUT__', 0 );
            $time = time ();
            if ($time > $timeout) {
                $form->setError ( 'captcha', '验证码已经超时.请重新输入.' );
                $goon = false;
            } else if ($code !== $code1) {
                $form->setError ( 'captcha', '验证码错误.请重新输入.' );
                $goon = false;
            } else {
                $tryCount = 0;
            }
        }
        if ($goon) {            
            $um = new CoreUserTable ();
            $account = $form ['account'];
            $passwod = $form ['passwd'];
            
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
                $tryCount ++;
                $_SESSION ['login_try_count'] = $tryCount;
            } else if (empty ( $user ['status'] )) {
                $form->setError ( 'account', '用户已锁定，请联系管理员.' );
            } else {
                $_SESSION ['login_try_count'] = 0;
                $loginInfo = new LoginInfo ( $user ['uid'], $user ['login'], time (), $_SERVER ['REMOTE_ADDR'] );
                $loginInfo->login ( true );
                LoginInfo::save ( $loginInfo );
                Response::redirect ( sess_del ( 'go_to_the_page_when_login', murl ( 'admin' ) ) );
            }
        }
    }
    $max_try_count = cfg ( 'max_try_count', 0 );
    $data = array ('form' => $form );
    if ($tryCount > $max_try_count) {
        $data ['captcha'] = true;
        $_SESSION ['login_need_captcha'] = true;
    } else {
        $_SESSION ['login_need_captcha'] = false;
    }
    return template ( 'admin/login.tpl', $data );
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
    $tryCount = sess_get ( 'login_try_count', 0 );
    $max_try_count = cfg ( 'max_try_count', 0 );
    $data = array ('form' => $form );
    if ($tryCount > $max_try_count) {
        $data ['captcha'] = true;
        $_SESSION ['login_need_captcha'] = true;
    } else {
        $_SESSION ['login_need_captcha'] = false;
    }    
    return template ( 'admin/login.tpl', $data );
}