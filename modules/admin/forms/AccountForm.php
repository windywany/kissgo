<?php
/**
 * 
 * @author Leo
 *
 */
class AccountForm extends BootstrapForm {
    var $uid = array (FWT_WIDGET => 'hidden' );
    var $login = array (FWT_LABEL => '用户账户', FWT_OPTIONS => array ('readonly' => 'readonly' ), FWT_VALIDATOR => array ('required' => '用户账户不能为空.', 'regexp(/^[a-z_][a-z0-9_\.]*$/i)' => '只能由字母,数字或下划线组成' ) );
    var $email = array (FWT_LABEL => '邮箱', FWT_VALIDATOR => array ('required' => '邮箱不能为空', 'email' => '必须是合法的邮箱.', 'callback(@check_user_email,uid)' => '邮箱已经被其它用户使用.' ) );
    var $passwd = array (FWT_LABEL => '密码', FWT_WIDGET => 'password', FWT_VALIDATOR => array ('rangelength(6,15)' => '密码长度至少6个字符最多15个字符.' ), FWT_TIP => '如果不修改密码，请留空.' );
    var $passwd1 = array (FWT_LABEL => '确认密码', FWT_WIDGET => 'password', FWT_VALIDATOR => array ('equalTo(passwd)' => '二次输入的密码必须一致.' ) );
    protected function getDefaultWidgetOptions() {
        return array (FWT_OPTIONS => array ('class' => 'input-xlarge' ), FWT_TIP_SHOW => FWT_TIP_SHOW_S );
    }
}