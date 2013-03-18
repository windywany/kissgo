<?php
/**
 * 用户账户表单
 * @author Leo
 * @date 2013-03-11 19:59
 */
class UserForm extends BootstrapForm {
    var $uid = array (FWT_WIDGET => 'hidden', FWT_INITIAL => 0 );
    var $login = array (FWT_LABEL => '用户账户', FWT_VALIDATOR => array ('required' => '用户账户不能为空.', 'regexp(/^[a-z_][a-z0-9_\.]*$/i)' => '只能由字母,数字或下划线组成', 'callback(@check_user_login,uid)' => '用户已经存在.' ) );
    var $email = array (FWT_LABEL => '邮箱', FWT_VALIDATOR => array ('required' => '邮箱不能为空', 'email' => '必须是合法的邮箱.', 'callback(@check_user_email,uid)' => '邮箱已经被其它用户使用.' ) );
    var $status = array (FWT_LABEL => '激活', FWT_WIDGET => 'radio', FWT_INITIAL => 1, FWT_BIND => '@getStatus', FWT_VALIDATOR => array ('required' => '请选择用户激活状态' ) );
    var $passwd = array (FWT_LABEL => '密码', FWT_WIDGET => 'password', FWT_VALIDATOR => array ('required' => '请填写密码', 'rangelength(6,15)' => '密码长度至少6个字符最多15个字符.' ) );
    var $passwd1 = array (FWT_LABEL => '确认密码', FWT_WIDGET => 'password', FWT_VALIDATOR => array ('equalTo(passwd)' => '二次输入的密码必须一致.' ) );
    public function getStatus($value = null, $data = array()) {
        
        return array ('1' => '激活', '0' => '禁用' );
    }
}