<?php
/**
 * 
 * Article Table
 * @author guangfeng.ning
 *
 */
class ArticleTable extends DbTable {
    var $table = 'article';
    public function schema() {
        $schema = new DbSchema ( 'articles, plain page' );
        $schema->addPrimarykey ( array ('aid' ) );
        $schema->addIndex ( 'IDX_IDX_NID', array ('nid' ) );
        $schema ['aid'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['nid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0 );
        $schema ['create_time'] = array ('type' => 'timestamp', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::AUTOINSERT_DATE );
        $schema ['create_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::LENGTH => 0, Idao::NN, Idao::UNSIGNED, Idao::AUTOINSERT_UID );
        $schema ['update_time'] = array ('type' => 'timestamp', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::AUTOINSERT_DATE, Idao::AUTOUPDATE_DATE );
        $schema ['update_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::AUTOINSERT_UID, Idao::AUTOUPDATE_UID, Idao::DEFT => 0 );
        $schema ['title'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 256, Idao::NN, Idao::CMMT => 'title' );
        $schema ['summary'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512 );
        $schema ['body'] = array ('type' => 'text', 'extra' => 'normal' );
        return $schema;
    }
}