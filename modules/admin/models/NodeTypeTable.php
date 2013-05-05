<?php
/**
 * node type
 * @author Leo
 *
 */
class NodeTypeTable extends DbTable {
    var $table = 'node_type';
    public function schema() {
        $schema = new DbSchema ( 'node types' );
        $schema->addPrimarykey ( array ('id' ) );
        $schema->addUnique ( 'UDX_TYPE', array ('type' ) );
        $schema ['id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN, Idao::CMMT => '类型' );
        $schema ['name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::NN, Idao::CMMT => '类型名称' );
        $schema ['creatable'] = array ('type' => 'bool', Idao::DEFT => true, Idao::CMMT => '是否可以创建' );
        $schema ['template'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => '模板文件' );
        $schema ['note'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512 );
        return $schema;
    }
}