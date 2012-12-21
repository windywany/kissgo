<?php
/** 
 * mapped to `user` table
 * 
 * @author Leo Ning
 *
 */
class UserEntity extends Model {
    var $uid_nak; //int(10) unsigned NOT NULL AUTO_INCREMENT,
    var $account_sr; //varchar(32) NOT NULL COMMENT '登录名，不区分大小写',
    var $passwd_sr; //char(32) NOT NULL COMMENT '密码',
    var $name_sr; //varchar(32) NOT NULL COMMENT '姓名，妮称',
    var $email_sr; //varchar(64) NOT NULL COMMENT '邮箱',
    var $status_nr = 0; //tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0：正常，1：禁用',
    var $reserved_nr = 0; //tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是系统内置用户',
    var $last_login_time_nr = 0; //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
    var $last_login_ip; //varchar(45) DEFAULT NULL COMMENT '最后登录IP',
}