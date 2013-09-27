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
        $schema->addIndex ( 'IDX_MENU_NAME', array ('menu_name', 'up_id' ) );
        
        $schema ['menuitem_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::UNSIGNED );
        $schema ['up_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '上级菜单项' );
        $schema ['menu_name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN, Idao::CMMT => '所属菜单' );
        $schema ['item_name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::CMMT => '菜单名称' );
        $schema ['type'] = array ('type' => 'enum', 'extra' => 'normal', Idao::NN, Idao::ENUM_VALUES => "url,page", Idao::CMMT => '菜单项类型' );
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
        $nodes = $this->query ( 'MNI.*,KNT.title as pagename', 'MNI' )->where ( array ('menu_name' => $menu_name, 'up_id' => $up_id ) )->sort ( 'sort', 'a' );
        $nodes->ljoin ( new KsgNodeTable (), 'MNI.page_id = KNT.nid', 'KNT' );
        return $nodes;
    }
    public function getIds($id, &$mids) {
        $ids = $this->query ( 'up_id' )->where ( array ('menuitem_id' => $id ) );
        $upid = $ids [0]['up_id'];
        if ($upid > 0 && ! isset ( $mids [$upid] )) {
            $mids [$upid] = true;
            $this->getIds ( $upid, $mids );
        }
    }
    public function crumb($mid, $flat = false) {
        if ($flat) {
            $menu = $this->query ( 'menuitem_id as id,menu_title,item_name as name,title,url,up_id', 'KMIT' )->where ( array ('menuitem_id' => $mid ) );
            $menu->ljoin ( new KsgMenuTable (), 'KMT.menu_name = KMIT.menu_name', 'KMT' );
        } else {
            $menu = $this->query ( 'menuitem_id as id,item_name as name,title,url,up_id', 'KMIT' )->where ( array ('menuitem_id' => $mid ) );
        }
        $menu = $menu [0];
        if ($menu) {
            $crumb [] = $menu;
            $this->_crumb ( $menu ['up_id'], $crumb );
            if ($flat) {
                $crumb [0] ['name'] = $crumb [0] ['name'] . '[' . $menu ['menu_title'] . ']';
            }
            return $crumb;
        }
        return array ('mid' => 0, 'name' => __ ( 'Home' ), 'url' => BASE_URL, 'title' => cfg ( 'site_name', '' ) );
    }
    private function _crumb($id, &$crumb) {
        if (empty ( $id )) {
            $menu = array ('mid' => 0, 'name' => __ ( 'Home' ), 'url' => BASE_URL, 'title' => cfg ( 'site_name', '' ) );
            array_unshift ( $crumb, $menu );
            return;
        }
        $menu = $this->query ( 'menuitem_id as id,item_name as name,title,url,up_id' )->where ( array ('menuitem_id' => $id ) );
        $menu = $menu [0];
        if ($menu) {
            array_unshift ( $crumb, $menu );
            $this->_crumb ( $menu ['up_id'], $crumb );
        }
    }
}