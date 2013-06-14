<?php
class KsgNodeTable extends DbTable {
    var $table = 'system_node';
    public function schema() {
        $schema = new DbSchema ( 'all nodes' );
        $schema->addPrimarykey ( array ('nid' ) );
        $schema->addIndex ( 'IDX_DEL_STATUS', array ('deleted', 'status' ) );
        $schema->addIndex ( 'IDX_NODE_TYPE', array ('node_type', 'node_id' ) );        
        $schema->addIndex ( 'IDX_UPDATE_TIME', array ('update_time' ) );
        $schema->addIndex ( 'IDX_PUBLISH_TIME', array ('publish_time' ) );
        //$schema->addIndex ( 'IDX_URL_SLUG', array ('url_slug' ) );
        $schema->addUnique ( 'IDX_URL_SLUG', array ('url_slug', 'nid' ) );
        
        $schema ['nid'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['deleted'] = array ('type' => 'bool', 'extra' => 'normal', Idao::NN, Idao::DEFT => false, Idao::CMMT => '是否删除' );
        
        $schema ['create_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::AUTOINSERT_UID, Idao::CMMT => '创建用户' );
        $schema ['create_time'] = array ('type' => 'timestamp', 'extra' => 'normal', Idao::NN, Idao::AUTOINSERT_DATE, Idao::CMMT => '创建时间' );
        $schema ['update_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::AUTOINSERT_UID, Idao::AUTOUPDATE_UID, Idao::CMMT => '修改用户' );
        $schema ['update_time'] = array ('type' => 'timestamp', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::AUTOINSERT_DATE, Idao::AUTOUPDATE_DATE, Idao::CMMT => '修改时间' );
        
        $schema ['cachetime'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '缓存时间，0不缓存。' );
        
        $schema ['publish_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '发布用户' );
        $schema ['publish_time'] = array ('type' => 'timestamp', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '发布时间' );
        
        $schema ['status'] = array ('type' => 'enum', 'extra' => 'normal', Idao::NN, Idao::ENUM_VALUES => "'draft','approving','approved','unapproved','published'", Idao::DEFT => 'draft', Idao::CMMT => '页面状态' );
        
        $schema ['commentable'] = array ('type' => 'bool', 'extra' => 'normal', Idao::NN, Idao::DEFT => false, Idao::CMMT => '允许评论,0：不允许，1：允许' );
        
        $schema ['title'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 128, Idao::CMMT => '页面的标题' );
        $schema ['subtitle'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 128, Idao::CMMT => '二级标题' );
        
        $schema ['ontopto'] = array ('type' => 'date', 'extra' => 'normal', Idao::CMMT => '在该日期以前，一直置顶' );
        
        $schema ['node_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '此页面对应的内容的编号。' );
        $schema ['node_type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::NN, Idao::LENGTH => 16, Idao::CMMT => '页面类型,用于主题中确定默认模板' );
        $schema ['template'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => '模板文件' );
        
        $schema ['author'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::CMMT => '作者' );
        $schema ['keywords'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 256, Idao::CMMT => '页面的keywords' );
        $schema ['description'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => '页面的描述' );
        
        $schema ['url_slug'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::CMMT => 'URL短标记，用于快速搜索' );
        $schema ['url'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => 'URL' );
        
        $schema ['source'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::CMMT => '来源' );
        $schema ['figure'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => '插图' );
        return $schema;
    }
}