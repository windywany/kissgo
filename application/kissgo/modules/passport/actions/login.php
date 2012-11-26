<?php
/**
 * 登录
 * User: Leo
 * Date: 12-11-24
 * Time: 下午1:01
 */
imports ( 'passport/forms/*' );

$req = Request::getInstance ();
if (Request::isPost ()) { //处理登录
	// TODO 登录功能实现
	$form = new PassportForm ();
	if ($form->valid ()) {
		$loginInfo = new LoginInfo ( 1, $form ['account'], time (), $_SERVER ['REMOTE_ADDR'] );
		$loginInfo->login ( true );
		LoginInfo::save ( $loginInfo );
		Response::redirect ( sess_del ( 'go_to_the_page_when_login', murl ( 'kissgo' ) ) );
	}
} else {
	$me = Passport::getPassport ();
	if ($me->isLogin ()) {
		Response::redirect ( sess_get ( 'go_to_the_page_when_login', BASE_URL ) );
	}
	if (! empty ( $req ['from'] )) {
		$_SESSION ['go_to_the_page_when_login'] = $req ['from'];
	}
	$form = new PassportForm ();
}
return template ( 'kissgo/passport/login.tpl', array ('form' => $form ) );