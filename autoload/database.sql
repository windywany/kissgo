SET SESSION FOREIGN_KEY_CHECKS=0;

/* Drop Tables */

DROP TABLE groups;
DROP TABLE nodes;
DROP TABLE users;


/* Create Tables */

CREATE TABLE groups
(
	gid int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Group Id',
	name varchar(32) NOT NULL UNIQUE COMMENT 'Group Name',
	note varchar(256) COMMENT 'Note',
	PRIMARY KEY (gid)
) ENGINE = InnoDB COMMENT = 'User Group : Groups' DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;


CREATE TABLE nodes
(
	id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
	uid bigint unsigned DEFAULT 0 NOT NULL COMMENT 'User Id',
	gid int unsigned NOT NULL COMMENT 'Group Id',
	deleted tinyint unsigned DEFAULT 0 NOT NULL COMMENT 'deleted',
	create_uid bigint unsigned DEFAULT 0 NOT NULL COMMENT 'Create User ID',
	create_time datetime NOT NULL COMMENT 'Create Time',
	update_uid bigint unsigned DEFAULT 0 NOT NULL COMMENT 'Modify User ID',
	update_time datetime NOT NULL COMMENT 'Last Modified',
	status smallint DEFAULT 1 NOT NULL COMMENT 'Status',
	linkto bigint unsigned DEFAULT 0 COMMENT 'linkto',
	content_type varchar(16) DEFAULT 'page' NOT NULL COMMENT 'Content Type',
	content_id varchar(32) NOT NULL COMMENT 'Content ID',
	name varchar(128) COMMENT 'Name',
	path varchar(512) NOT NULL COMMENT 'Path',
	filename_tpl varchar(64) COMMENT 'File Name Template',
	filename varchar(32) NOT NULL COMMENT 'File Name',
	template varchar(512) COMMENT 'Template File',
	ontop datetime COMMENT 'On top',
	cache_time int unsigned DEFAULT 0 COMMENT 'Cache Time',
	commentable tinyint DEFAULT 0 COMMENT 'Commentable',
	title varchar(512) COMMENT 'title',
	source varchar(128) COMMENT 'Source',
	author varchar(128) COMMENT 'Author',
	description varchar(512) COMMENT 'Description',
	keywords varchar(512) COMMENT 'keywords',
	figure varchar(512) COMMENT 'Figure',
	content text COMMENT 'Content',
	PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Nodes : web page nodes' DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;


CREATE TABLE users
(
	id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
	gid int unsigned NOT NULL COMMENT 'Group Id',
	username varchar(32) NOT NULL UNIQUE COMMENT 'User Name : 用户名登录名',
	display_name varchar(128) COMMENT 'Display Name',
	email varchar(128) NOT NULL UNIQUE COMMENT 'email',
	passwd char(32) NOT NULL COMMENT 'Password',
	status tinyint DEFAULT 1 NOT NULL COMMENT 'Status : 0 - locked
1 - active',
	last_ip varchar(128) COMMENT 'Last log-in IP',
	last_time datetime COMMENT 'Last Log-in Time',
	PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Users : administrator users' DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;



/* Create Indexes */

CREATE INDEX IDX_DELETED ON nodes (deleted ASC);
CREATE INDEX IDX_STATUS ON nodes (status ASC);
CREATE INDEX IDX_PATH ON nodes (path ASC, filename ASC);
CREATE UNIQUE INDEX UDX_CONTENT ON nodes (content_type ASC, content_id ASC);



