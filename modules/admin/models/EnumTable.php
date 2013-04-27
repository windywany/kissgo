<?php
/**
 * 
 * @author Leo
 *
 */
class EnumTable extends DbTable {
    var $table = 'enums';
    public function schema() {
        $schema = new DbSchema ( 'enums' );
        $schema->addPrimarykey ( array ('enum_id' ) );
        $schema->addIndex ( 'IDX_ENUM_TYPE', array ('type' ) );
        $schema ['enum_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN );
        $schema ['enum_value'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 128, Idao::NN );
        return $schema;
    }
}