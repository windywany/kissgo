<?php
/**
 * the roles of system
 * @author Leo
 *
 */
class KsgRoleTable extends DbTable {
    var $table = 'system_role';
    public function schema() {
        $schema = new DbSchema ( 'the roles of system' );
        
        $schema->addPrimarykey ( 'rid' );
        
        $schema->addUnique ( 'UIDX_ROLE_DELETED', array ('deleted', 'label' ) );
        
        $schema ['rid'] = array ('type' => 'serial', Idao::UNSIGNED, Idao::TYPE_EXTRA => Idao::TE_SMALL );
        $schema ['deleted'] = array ('type' => 'bool', Idao::CMMT => '1:deleted', Idao::DEFT => false );
        $schema ['label'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 32, Idao::NN );
        $schema ['name'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 128, Idao::NN );
        $schema ['reserved'] = array ('type' => 'bool', Idao::DEFT => false, Idao::NN, Idao::CMMT => '1:yes;0:no;' );
        $schema ['note'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 512 );
        return $schema;
    }
}