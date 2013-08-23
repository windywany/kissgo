<?php
class MediaTable extends DbTable {
    var $table = 'system_media';
    public function schema() {
        $schema = new DbSchema ( 'medias' );
        
        $schema->addPrimarykey ( array ('fid' ) );
        
        $schema->addIndex ( 'IDX_IDX_TYPE', array ('type','pfid' ) );
        $schema->addIndex ( 'IDX_IDX_CREATE_TIME', array ('create_time' ) );
        
        $schema ['fid'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['create_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '用户' );
        $schema ['create_time'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '时间' );        
        $schema ['hasthumbnail'] = array ('type' => 'int', 'extra' => 'small', Idao::LENGTH => 4, Idao::DEFT => 0, Idao::CMMT => '缩略图的数量' );
        $schema ['type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::CMMT => '类型' );
        $schema ['mine_type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::CMMT => '附件多媒体类型' );
        $schema ['name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 256, Idao::CMMT => '文件名' );
        $schema ['alt_text'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::CMMT => 'alt文本' );
        $schema ['url'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 1024, Idao::CMMT => '文件URL，相对与根' );
        $schema ['ext'] = array ('type' => 'varchar', 'extra' => 'normal',Idao::DEFT=>'',Idao::LENGTH => 10, Idao::CMMT => '扩展名' );

        return $schema;
    }
}

