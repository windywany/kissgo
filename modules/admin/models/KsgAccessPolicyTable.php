<?php
/**
 * access policy
 * @author Leo
 *
 */
class KsgAccessPolicyTable extends DbTable {
    var $table = 'system_acl';
    public function schema() {
        $schema = new DbSchema ( "access policy" );
        $schema->addPrimarykey ( 'id' );
        $schema->addUnique ( 'UIDX_POLICY', array ('atype', 'aid', 'resource', 'action' ) );
        
        $schema ['id'] = array ('type' => 'serial', Idao::UNSIGNED, Idao::NN );
        $schema ['atype'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 32, Idao::NN, Idao::CMMT => '访问者类型' );
        $schema ['aid'] = array ('type' => 'int', Idao::UNSIGNED, Idao::NN, Idao::CMMT => '访问者编号' );
        $schema ['resource'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 64, Idao::NN, Idao::CMMT => '资源' );
        $schema ['action'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 32, Idao::NN, Idao::CMMT => '操作' );
        $schema ['allow'] = array ('type' => 'bool', Idao::UNSIGNED, Idao::NN, Idao::DEFT => 0, Idao::CMMT => '是否允许' );
        $schema ['extra'] = array ('type' => 'text', 'extra' => Idao::TE_BIG, Idao::CMMT => '额外信息' );
        return $schema;
    }
}