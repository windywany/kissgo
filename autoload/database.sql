SET SESSION FOREIGN_KEY_CHECKS=0;

/* Drop Indexes */

DROP INDEX IDX_VISIBLE ON nodes;



/* Drop Tables */

DROP TABLE comments;
DROP TABLE nodes;
DROP TABLE users;




/* Create Tables */

CREATE TABLE comments
(

);


-- web page nodes
CREATE TABLE nodes
(
	id bigint unsigned NOT NULL AUTO_INCREMENT,
	type varchar(16) DEFAULT 'page' NOT NULL,
	visible tinyint DEFAULT 1 NOT NULL,
	key char(32) NOT NULL UNIQUE,
	url varchar(1024) NOT NULL,
	title varchar(1024),
	description varchar(512),
	keywords varchar(512),
	tpl varchar(512),
	PRIMARY KEY (id),
	CONSTRAINT UDX_TYPE UNIQUE (id, type)
) ENGINE = InnoDB COMMENT = 'web page nodes' DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;


-- administrator users
CREATE TABLE users
(
	id bigint unsigned NOT NULL AUTO_INCREMENT,
	-- 用户名登录名
	username varchar(32) NOT NULL UNIQUE COMMENT '用户名登录名',
	passwd char(32) NOT NULL,
	display_name varchar(128),
	email varchar(128) NOT NULL UNIQUE,
	-- 0 - locked
	-- 1 - active
	status tinyint DEFAULT 1 NOT NULL COMMENT '0 - locked
1 - active',
	last_ip varchar(128),
	last_time datetime,
	PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'administrator users' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;



/* Create Indexes */

CREATE INDEX IDX_VISIBLE ON nodes (visible ASC);



