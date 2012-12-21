<?php
$scheme = KissGoSetting::getSetting ( 'scheme' );

$scheme ['user'] = "CREATE TABLE `%PREFIX%user` (
    `uid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `account` VARCHAR(32) NOT NULL COMMENT '登录名，不区分大小写',
    `passwd` CHAR(32) NOT NULL COMMENT '密码',
    `name` VARCHAR(32) NOT NULL COMMENT '姓名，妮称',
    `email` VARCHAR(64) NOT NULL COMMENT '邮箱',
    `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态，0：正常，1：禁用',
    `reserved` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是系统内置用户',
    `last_login_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
    `last_login_ip` VARCHAR(45) NULL COMMENT '最后登录IP',
    PRIMARY KEY (`uid`),
    UNIQUE INDEX `IDX_UN_UNAME` (`account` ASC),
    UNIQUE INDEX `IDX_UN_EMAIL` (`email` ASC),
    INDEX `IDX_STATUS` (`status` ASC)
)  ENGINE=%ENGINE% DEFAULT CHARACTER SET=utf8 COMMENT='The system users'";

$scheme ['role'] = "CREATE TABLE `%PREFIX%role` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '组编号，主键',
    `label` VARCHAR(45) NOT NULL COMMENT '用户组标识',
    `name` VARCHAR(45) NOT NULL COMMENT '组名',
    `reserved` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是系统内置角色',
    `note` VARCHAR(256) NULL COMMENT '备注说明',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `IDX_U_LABEL` (`label` ASC)
)  ENGINE=%ENGINE% DEFAULT CHARACTER SET=utf8 COMMENT='用户组'";

$scheme ['user_role'] = "CREATE TABLE `%PREFIX%user_role` (
    `rid` INT UNSIGNED NOT NULL COMMENT '组编号',
    `uid` INT UNSIGNED NOT NULL COMMENT '用户编号',
    PRIMARY KEY (`rid` , `uid`)
)  ENGINE=%ENGINE% DEFAULT CHARACTER SET=utf8 COMMENT='用户的角色'";

$scheme ['preference'] = "CREATE TABLE `%PREFIX%preference` (
    option_id INT UNSIGNED NULL AUTO_INCREMENT,
    option_uid INT UNSIGNED ZEROFILL NOT NULL DEFAULT 0 COMMENT '用户ID,0为系统选项.',
    option_group VARCHAR(16) NOT NULL DEFAULT 'core' COMMENT '选项组',
    option_name VARCHAR(16) NOT NULL COMMENT '选项名',
    option_value LONGTEXT NULL COMMENT '选项值',
    PRIMARY KEY (option_id),
    UNIQUE INDEX IDU_OPT_NAME (option_uid ASC , option_group ASC , option_name ASC)
)  ENGINE=%ENGINE% DEFAULT CHARACTER SET=utf8 COMMENT='系统选项'";

$scheme ['authorization'] = "CREATE TABLE `%PREFIX%authorization` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '记录编号',
    `atype` VARCHAR(16) NOT NULL COMMENT '访问者类型',
    `aid` INT UNSIGNED NOT NULL COMMENT '访问者编号',
    `resource` VARCHAR(64) NOT NULL COMMENT '资源',
    `action` VARCHAR(16) NOT NULL COMMENT '操作',
    `priority` SMALLINT(4) UNSIGNED NOT NULL DEFAULT 100 COMMENT '优先级',
    `allow` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否允许',
    `extra` LONGTEXT NULL COMMENT '额外信息',
    PRIMARY KEY (`id`),
    INDEX `ID_ROLE` (`atype` ASC , `aid` ASC)
)  ENGINE=%ENGINE% DEFAULT CHARACTER SET=utf8 COMMENT='系统授权表'";

//end of install_core_scheme.php