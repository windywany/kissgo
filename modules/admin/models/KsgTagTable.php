<?php
/**
 * 
 * @author Leo
 *
 */
class KsgTagTable extends DbTable {
    var $table = 'system_tag';
    public function schema() {
        $schema = new DbSchema ( 'tags' );
        $schema->addPrimarykey ( array ('tag_id' ) );
        $schema->addIndex ( 'IDX_TAG_TYPE', array ('type' ) );
        $schema ['tag_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN );
        $schema ['tag'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::NN );
        $schema ['slug'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::NN );
        return $schema;
    }
}