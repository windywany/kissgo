<?php
assert_login ();
/**
 *
 * @param Request $req
 * @param Response $res
 * @return View
*/
function do_admin_menus_add_post($req, $res) {
    $type = rqst ( 'type', 'url' );
    $target = rqst ( 'target', '_blank' );
    $menu_name = trim ( rqst ( 'menu_name' ) );
    if (empty ( $menu_name )) {
        echo "error:未指定菜单";
        Response::close ( true );
    }
    $items = array ();
    if ($type == 'url') {
        $items = do_admin_menus_add_url_item ( $menu_name, $target );
    } else if ($type == 'page') {
        $items = do_admin_menus_add_page_item ( $menu_name, $target );
    } else {
        log_warn ( '菜单项类型不正确.' );
    }
    if (empty ( $items )) {
        echo 'error:未能成功添加菜单项.具体原因请参考日志.';
        Response::getInstance ()->close ( true );
    }
    $data ['items'] = $items;
    $data ['adding'] = true;
    $data ['_CUR_URL'] = murl ( 'admin', 'menus' );
    return view ( 'admin/views/menus/item.tpl', $data );
}
function do_admin_menus_add_url_item($menu_name, $target) {
    $form = new KsgMenuItemForm ();
    $items = array ();
    if ($form->validate ()) {
        $item = $form->getCleanData ();
        $item ['type'] = 'url';
        $item ['up_id'] = 0;
        $item ['sort'] = 999;
        $miM = new KsgMenuItemTable ();
        $rst = $miM->insert ( $item );
        if ($rst != false) {
            $rst ['type_name'] = '自定义';
            $items [] = $rst;
        }
    } else {
        echo 'error:' . $form->getError ( "\n" );
        Response::getInstance ()->close ( true );
    }
    return $items;
}
function do_admin_menus_add_page_item($menu_name, $target) {
    $items = array ();
    $ids = safe_ids ( rqst ( 'ids' ), ',', true );
    if (empty ( $ids )) {
        return $items;
    }
    $item ['menu_name'] = $menu_name;
    $item ['target'] = $target;
    $item ['type'] = 'page';
    $item ['sort'] = 999;
    $item ['up_id'] = 0;
    $pM = new KsgNodeTable ();
    $miM = new KsgMenuItemTable ();
    foreach ( $ids as $pid ) {
        if (empty ( $pid )) {
            continue;
        }
        $page = $pM->read ( array ('nid' => $pid ) );
        if (! $page) {
            continue;
        }
        $item ['page_id'] = $pid;
        $item ['item_name'] = empty ( $page ['title'] ) ? '未命名' : $page ['title'];
        $item ['title'] = $page ['subtitle'];
        $item = $miM->insert ( $item );
        if ($item) {
            $item ['type_name'] = '页面';
            $items [] = $item;
        }
    }
    return $items;
}