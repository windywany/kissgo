<?php
assert_login ();
/**
 *
 * @param Request $req
 * @param Response $res
 * @return View
*/
function do_admin_menus_del_get($req, $res) {
    if (isset ( $req ['miid'] )) {
        $miid = irqst ( 'miid' );
        if (empty ( $miid )) {
            show_page_tip ( '<strong>糟糕!</strong><br/>错误的菜单项编号', 'error' );
            Response::back ();
        }
        $miM = new KsgMenuItemTable ();
        $where = array ('menuitem_id' => $miid );
        $item = $miM->read ( $where );
        $rst = $miM->remove ( $where );
        if ($rst != false) {
            $miM->update ( array ('up_id' => $item ['up_id'] ), array ('up_id' => $miid ) );
        } else {
            show_page_tip ( '<strong>糟糕!</strong><br/>无法删除菜单项:' . db_error ( true ), 'error' );
        }
    } else if (isset ( $req ['mn'] )) {
        $mn = rqst ( 'mn' );
        if (empty ( $mn )) {
            show_page_tip ( '<strong>糟糕!</strong><br/>非法的导航菜单', 'error' );
            Response::back ();
        }
        $mM = new KsgMenuTable ();
        $miM = new KsgMenuItemTable ();
        $mM->getDialect ()->beginTransaction ();
        $where ['menu_name'] = $mn;
        $rst = $mM->remove ( $where );
        $rst = $rst != false ? $miM->remove ( $where ) : false;
        if ($rst != false) {
            $mM->getDialect ()->commit ();
            Response::redirect ( murl ( 'admin', 'menus' ) );
        } else {
            show_page_tip ( '<strong>糟糕!</strong><br/>删除导航菜单出错:' . db_error ( true ), 'error' );
            $mM->getDialect ()->rollBack ();
        }
    } else {
        show_page_tip ( '<strong>糟糕!</strong><br/>未知操作.', 'error' );
    }
    Response::back ();
}