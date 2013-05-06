<?php
/**
 * Tag table
 * @author Leo
 *
 */
class TagTable extends DbTable {
    var $table = 'tag';
    public function schema() {
        $schema = new DbSchema ( 'tags' );
        $schema->addPrimarykey ( array ('tag_id' ) );
        $schema->addUnique ( 'UDX_TAG_TAG', array ('tag' ) );
        $schema ['tag_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['tag'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN );
        $schema ['type'] = array ('type' => 'int', 'extra' => 'small', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '1:flag; 0:tag' );
        return $schema;
    }
    public function getFlags() {
        $rst = $this->query ( 'tag_id,tag' )->where ( array ('type' => 1 ) );
        return $rst;
    }
    public function getTags() {
        $rst = $this->query ( 'tag_id,tag' )->where ( array ('type' => 0 ) );
        return $rst;
    }
}