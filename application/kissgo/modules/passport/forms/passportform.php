<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Leo
 * Date: 12-11-25
 * Time: 下午8:30
 * To change this template use File | Settings | File Templates.
 */
class PassportForm extends TableForm {
    var $account = array('label' => '用户名', 'validator' => array('required' => '用户名不能为空.'));
    var $passwd = array('label' => '密码', 'validator' => array('required' => '密码不能为空', 'maxlength(15)' => '密码最大长度为15个字符.', 'minlength(6)' => '密码最小长度为6个字符.'));
    var $captcha = array('label' => '验证码');
}