<?php
/**
 * 导航菜单
 * @author Leo
 *
 */
class KsgMenuTable extends DbTable {
    var $table = 'system_menu';
    public function schema() {
        $schema = new DbSchema ( 'navigation menus' );
        $schema->addPrimarykey ( array ('menu_id' ) );
        $schema->addUnique ( 'UDX_UDX_MENU_NAME', array ('menu_name' ) );
        $schema ['menu_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::CMMT => '主键' );
        $schema ['menu_name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN, Idao::CMMT => '菜单名称' );
        $schema ['menu_title'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::NN, Idao::CMMT => '菜单标题' );
        $schema ['menu_default'] = array ('type' => 'bool', 'extra' => 'normal', Idao::NN, Idao::DEFT => 0, Idao::CMMT => '是否是默认导航菜单' );
        return $schema;
    }
}