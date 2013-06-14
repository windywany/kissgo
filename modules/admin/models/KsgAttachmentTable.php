<?php
class KsgAttachmentTable extends DbTable {
    var $table = 'system_attachment';
    public function schema() {
        $schema = new DbSchema ( 'attachments' );
        
        $schema->addPrimarykey ( array ('attachment_id' ) );
        
        $schema->addIndex ( 'IDX_IDX_TYPE', array ('type' ) );
        $schema->addIndex ( 'IDX_IDX_CREATE_TIME', array ('create_time' ) );
        
        $schema ['attachment_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['create_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '上传用户' );
        $schema ['create_time'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '上传时间' );
        $schema ['linked'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '是否被引用' );
        $schema ['hasthumbnail'] = array ('type' => 'int', 'extra' => 'small', Idao::LENGTH => 4, Idao::DEFT => 0, Idao::CMMT => '缩略图的数量' );
        $schema ['type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::CMMT => '附件类型' );
        $schema ['mine_type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::CMMT => '附件多媒体类型' );
        $schema ['name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 256, Idao::CMMT => '附件媒体名' );
        $schema ['alt_text'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::CMMT => 'alt文本' );
        $schema ['url'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 1024, Idao::CMMT => '文件URL，相对与根' );
        $schema ['ext'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 10, Idao::CMMT => '扩展名' );

        return $schema;
    }
}

