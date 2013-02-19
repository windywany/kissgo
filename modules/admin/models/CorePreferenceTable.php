<?php
/**
 * system preference
 * @author Leo
 *
 */
class CorePreferenceTable extends DbTable {
    var $table = 'preference';
    public function schema() {
        $schema = new DbSchema ( "system preference" );
        
        $schema->addPrimarykey ( 'pid' );
        
        $schema->addUnique ( 'UIDX_GROUP_NAME', array ('group', 'name' ) );
        
        $schema ['pid'] = array ('type' => 'serial', Idao::UNSIGNED );
        $schema ['group'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 16, Idao::NN );
        $schema ['name'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 32, Idao::NN );
        $schema ['value'] = array ('type' => Idao::TYPE_TEXT, Idao::TYPE_EXTRA => Idao::TE_BIG, Idao::NN );
        
        return $schema;
    }
}