<?php
assert_login ();
/**
 * 
 * @param Request $req
 * @param Response $res
 * @return View
 */
function do_admin_menus_get($req, $res) {
    $data = array ();
    $data = array ();
    $op = rqst ( 'op' );
    $menu_id = irqst ( 'mid', 0 );
    
    $mM = new KsgMenuTable ();
    $menus = $mM->query ();
    
    $menu = sess_del ( 'menu_info', array () );
    $menuitems = array ();
    if (! empty ( $menu_id )) {
        $menu = $mM->read ( array ('menu_id' => $menu_id ) );
        $miM = new KsgMenuItemTable ();
        $menuitems = $miM->getSubItems ( $menu ['menu_name'], 0 );
        $menuitems = $menuitems->size () > 0 ? $menuitems : array ();
        $data ['menu'] = $menu;
    } else {
        $op = 'add';
    }
    
    $data ['items'] = $menuitems;
    $data ['menus'] = $menus;
    
    $pM = new KsgNodeTable ();
    $pages = $pM->query ( 'nid,title' )->where ( array ('deleted' => 0, 'status' => 'published' ) )->sort ( 'publish_time' )->limit ( 1, 10 );
    $data ['npages'] = $pages;
    
    $data ['op'] = $op;
    $data ['menu_id'] = $menu_id;
    $data ['_CUR_URL'] = murl ( 'admin', 'menus' );
    
    bind ( 'output_menu_items', '_hook_for_output_menu_items', 10, 2 );
    
    return view ( 'admin/views/menus/menus.tpl', $data );
}
//保存菜单
function do_admin_menus_post($req, $res) {
    $menu_url = murl ( 'admin', 'menus' );
    $form = new KsgMenuForm ();
    if (! $form->validate ()) {
        show_page_tip ( '<strong>糟糕!</strong>' . $form->getError ( '<br/>' ), 'error' );
        $_SESSION ['menu_info'] = $form->getCleanData ();
        Response::redirect ( $menu_url );
    }
    $menu = $form->getCleanData ();
    $mid = 0;
    if (! empty ( $menu ['menu_id'] )) {
        $mid = $menu ['menu_id'];
    }
    unset ( $menu ['menu_id'] );
    $md = isset ( $req ['menu_default'] );
    if ($md) {
        $menu ['menu_default'] = 1;
    }
    $mM = new KsgMenuTable ();
    $mM->getDialect ()->beginTransaction ();
    if (! $mid) {
        $rst = $mM->insert ( $menu );
        if ($rst) {
            $mid = $rst ['menu_id'];
        }
    } else {
        $rst = $mM->update ( $menu, array ('menu_id' => $mid ) );
    }
    if (! empty ( $rst )) {
        if ($md) {
            $mM->update ( array ('menu_default' => 0 ), array ('menu_id !=' => $mid ) );
        }
        if (isset ( $req ['item'] )) {
            $rst = save_menu_items ();
        }
        if ($rst) {
            $mM->getDialect ()->commit ();
            Response::redirect ( $menu_url . '?mid=' . $mid );
        }
    }
    $mM->getDialect ()->rollBack ();
    show_page_tip ( '<strong>糟糕!</strong>' . $form->getError ( '<br/>' ), 'error' );
    $menu ['menu_id'] = $mid;
    $_SESSION ['menu_info'] = $menu;
    Response::redirect ( $menu_url );
}
//只在菜单项
function save_menu_items() {
    $items = rqst ( 'item', array () );
    if (empty ( $items )) {
        return true;
    }
    $miM = new KsgMenuItemTable ();
    foreach ( $items as $item_id => $item ) {
        $rst = $miM->update ( $item, array ('menuitem_id' => $item_id ) );
        if (! $rst) {
            return false;
        }
    }
    return true;
}
//输出菜单项
function _hook_for_output_menu_items($html, $items) {
    static $miM = false, $menuitem_types = array ('url' => '自定义', 'page' => '页面', 'cate' => '栏目' );
    if (! $miM) {
        $miM = new KsgMenuItemTable ();
    }
    if (! $items) {
        return $html;
    }
    foreach ( $items as $item ) {
        $item ['type_name'] = $menuitem_types [$item ['type']];
        $view = view ( 'admin/views/menus/item.tpl', array ('_CUR_URL' => murl ( 'admin', 'menus' ), 'adding' => false, 'items' => array ($item ) ) );
        $itemText = $view->render ();
        $html .= '<li id="menu-item-' . $item ['menuitem_id'] . '">' . $itemText;
        $subitems = $miM->getSubItems ( $item ['menu_name'], $item ['menuitem_id'] );
        if ($subitems && count ( $subitems ) > 0) {
            $html .= '<ol>';
            $html .= _hook_for_output_menu_items ( '', $subitems );
            $html .= '</ol>';
        }
        $html .= '</li>';
    }
    return $html;
}