<?php
/**
 * 发送邮件设置
 * @author Leo
 *
 */
class SmtpPerferenceForm extends BootstrapForm {
    var $smtp_type = array (FWT_LABEL => '发送方式', FWT_WIDGET => 'radio', FWT_BIND => '@getSmtpType', FWT_NO_APPLY => true, FWT_INITIAL => 'smtp' );
    var $smtp_secure = array (FWT_LABEL => '安全发送', FWT_WIDGET => 'radio', FWT_BIND => '@getSSLSupport', FWT_NO_APPLY => true );
    var $smtp_host = array (FWT_LABEL => '主机', FWT_TIP => '邮件服务器的域名或IP' );
    var $smtp_port = array (FWT_LABEL => '端口', FWT_TIP => '邮件服务器的端口，默认为25.', FWT_VALIDATOR => array ('digits' => '请输入合法的端口号,否则可能不能正常发送邮件.' ) );
    var $smtp_from = array (FWT_LABEL => '发件人姓名', FWT_TIP => '显示在发件人一栏.' );
    var $smtp_reply = array (FWT_LABEL => '回复地址', FWT_TIP => '用于接收回复邮件的地址，一般与邮件账户相同.', FWT_VALIDATOR => array ('email' => '请输入合法的邮件地址.' ) );
    var $smtp_user = array (FWT_LABEL => '邮件账户', FWT_TIP => '登录邮件系统的账户.' );
    var $smtp_passwd = array (FWT_LABEL => '账户密码', FWT_WIDGET => 'password', FWT_TIP => '登录邮件系统的密码.' );
    var $smtp_test_email = array (FWT_LABEL => '测试邮件地址', FWT_TIP => '输入测试邮件地址,然后点击‘测试邮件’按钮.', FWT_VALIDATOR => array ('email' => '请输入合法的邮件地址.' ) );
    private $_test_email = false;
    // 发送方式
    public function getSmtpType($value, $data) {
        return array ('smtp' => 'SMTP', 'mail' => 'PHP mail函数', 'sendmail' => 'sendmail', 'qmail' => 'qmail' );
    }
    // 安全链接
    public function getSSLSupport($value, $data) {
        return array ('' => '不使用安全链接', 'ssl' => '使用SSL', 'tls' => '使用TLS' );
    }
    //启用测试邮件
    public function setTestEmail() {
        $this->_test_email = true;
    }
    protected function getFormFoot() {
        if ($this->_test_email) {
            $test = '<div class="form-actions"><a href="#" class="btn btn-primary" id="btn-test-email">测试邮件</a></div>';
        }
        $foot = parent::getFormFoot ();
        return $test . $foot;
    }
    
    // 设置默认
    protected function getDefaultWidgetOptions() {
        return array (FWT_OPTIONS => array ('class' => 'span5' ), FWT_TIP_SHOW => FWT_TIP_SHOW_S );
    }
}