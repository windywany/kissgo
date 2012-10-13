<?php
// 用户
class CoreUserModel extends Model {
	var $_table = 'core_user';
	
	var $uid_nak; // INT UNSIGNED NOT NULL AUTO_INCREMENT,
	var $uname_r; // VARCHAR(32) NOT NULL COMMENT '登录名，不区分大小写',
	var $passwd_r; // CHAR(32) NOT NULL COMMENT '密码',
	var $name_r; // VARCHAR(32) NOT NULL COMMENT '姓名，妮称',
	var $email_r; // VARCHAR(64) NOT NULL COMMENT '邮箱',
	var $status_rn = 0; // TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT
	// '状态，0：正常，1：禁用',
	var $reserved_nr = 0; // 是否是系统内置用户,	

	//检验用户账户是否存在
	public function check_uname($value, $data) {
		$where ['uname'] = $value;
		if (! empty ( $data ['uid'] )) {
			$where ['uid !='] = $data ['uid'];
		}
		return $this->exist ( $where ) ? "用户账户已经存在" : true;
	}
	//检验邮箱是否存在
	public function check_email($value, $data) {
		$where ['email'] = $value;
		if (! empty ( $data ['uid'] )) {
			$where ['uid !='] = $data ['uid'];
		}
		return $this->exist ( $where ) ? "邮箱地址已经存在" : true;
	}
}