<?php
/**
 * 导航菜单项
 * @author Leo
 *
 */
class KsgMenuItemTable extends DbTable {
    var $table = 'system_menuitem';
    public function schema() {
        $schema = new DbSchema ( 'navigation menu items' );
        $schema->addPrimarykey ( array ('menuitem_id' ) );
        $schema->addIndex ( 'IDX_MENU_NAME', array ('menu_name', 'is_navi', 'up_id' ) );
        $schema ['menuitem_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::UNSIGNED );
        $schema ['up_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '上级菜单项' );
        $schema ['menu_name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN, Idao::CMMT => '所属菜单' );
        $schema ['item_name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::CMMT => '菜单名称' );
        $schema ['type'] = array ('type' => 'enum', 'extra' => 'normal', Idao::NN, Idao::ENUM_VALUES => "url,page,path", Idao::CMMT => '菜单项类型' );
        $schema ['page_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '相对应的页面ID' );
        $schema ['sort'] = array ('type' => 'int', 'extra' => 'small', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 999, Idao::CMMT => '排序' );
        $schema ['target'] = array ('type' => 'enum', 'extra' => 'normal', Idao::NN, Idao::ENUM_VALUES => "_blank,_self", Idao::DEFT => '_self', Idao::CMMT => '打开网页的目标' );
        $schema ['title'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::CMMT => '提示' );
        $schema ['url'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => '自定义URL' );
        return $schema;
    }
    public function getMenuItems($menu_name) {
        $nodes = $this->query ()->where ( array ('menu_name' => $menu_name ) )->sort ( 'sort', 'a' );
        return $nodes;
    }
    public function getSubItems($menu_name, $up_id) {
        $nodes = $this->query ()->where ( array ('menu_name' => $menu_name, 'up_id' => $up_id ) )->sort ( 'sort', 'a' );
        return $nodes;
    }
    public function addToMenuItem($nid) {

    }
}