<?php
class CoreAttachmentTable extends DbTable {
    var $table = 'attachment';
    public function schema() {
        $schema = new DbSchema ( "attachments" );
        $schema->addPrimarykey ( 'attachment_id' );
        $schema->addIndex ( 'IDX_TYPE', 'type' );
        $schema->addIndex ( 'IDX_CREATE_TIME', 'create_time' );
        $schema ['attachment_id'] = array ();
        /*
         `attachment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `create_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传用户',
          `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
          `linked` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否被引用',
          `hasthumbnail` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '缩略图的数量',
          `type` varchar(16) DEFAULT NULL COMMENT '附件类型',
          `mine_type` varchar(32) DEFAULT NULL COMMENT '附件多媒体类型',
          `name` varchar(256) DEFAULT NULL COMMENT '附件媒体名',
          `ext` varchar(10) DEFAULT NULL COMMENT '扩展名',
          `alt_text` varchar(64) DEFAULT NULL COMMENT 'alt文本',
          `url` varchar(256) DEFAULT NULL COMMENT '文件URL，相对与根',
          PRIMARY KEY (`attachment_id`),
          KEY `IDX_TYPE` (`type`),
          KEY `IDX_CREATE_TIME` (`create_time`)
         */
        return $schema;
    }
}