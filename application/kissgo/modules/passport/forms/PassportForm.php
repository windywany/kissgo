<?php
/*
 * 登录表单
 * 
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo.libs
 *
 * $Id$
 */
class PassportForm extends TableForm {
	var $account = array ('label' => '用户名', 'validator' => array ('required' => '用户名不能为空.' ) );
	var $passwd = array ('label' => '密码', 'validator' => array ('required' => '密码不能为空', 'maxlength(15)' => '密码最大长度为15个字符.', 'minlength(6)' => '密码最小长度为6个字符.' ) );
	var $captcha = array ('label' => '验证码' );
	var $lang = array ('label' => 'Language' );
}