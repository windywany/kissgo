<?php
/**
 * the relationship between role and user
 * @author Leo
 *
 */
class CoreUserRoleTable extends DbTable {
    var $table = 'user_role';
    public function schema() {
        $schema = new DbSchema ( "the relationship between role and user" );
        
        $schema->addPrimarykey ( array ('rid', 'uid' ) );
        
        $schema ['rid'] = array ('type' => 'int', Idao::UNSIGNED, Idao::TYPE_EXTRA => Idao::TE_SMALL );
        $schema ['uid'] = array ('type' => 'int', Idao::UNSIGNED );
        $schema ['sort'] = array ('type' => 'int', Idao::UNSIGNED, 'extra' => Idao::TE_SMALL, Idao::NN );
        
        return $schema;
    }
}