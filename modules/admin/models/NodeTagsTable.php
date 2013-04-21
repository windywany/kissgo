<?php
class NodeTagsTable extends DbTable {
    var $table = 'node_tag';
    public function schema() {
        $schema = new DbSchema ( 'node tags' );
        $schema->addPrimarykey ( array ('node_id', 'tag_id' ) );
        $schema ['node_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['tag_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        return $schema;
    }
}

