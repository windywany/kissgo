<?php

/**
 * menu controller
 *
 * @author Leo
 *
 */
class AdminMenuController extends Controller {

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }

    public function index() {
        $data = array ();
        $menu = dbselect ( '*' )->from ( '{menus}' );
        count ( $menu );
        $data ['menus'] = $menu;
        return view ( 'menu/menu.tpl', $data );
    }

    public function add() {
        $form = new MenuForm ();
        $data ['validateRule'] = $form->rules ();
        return view ( 'menu/add_menu.tpl', $data );
    }

    public function edit($id) {
        $menu = dbselect ( '*' )->from ( '{menus}' )->where ( array ('id' => intval ( $id ) ) );
        if (count ( $menu )) {
            $menu = $menu [0];
        } else {
            $menu = array ();
        }
        $data ['menu'] = $menu;
        $form = new MenuForm ( $menu );
        $form->removeRlue ( 'alias', 'callback(@chkalias,id)' );
        $data ['validateRule'] = $form->rules ();
        $data ['items'] = dbselect ( '*' )->from ( '{menuitems}' )->where ( array ('parent' => 0, 'menu' => $menu ['alias'] ) )->asc ( 'sort' );
        bind ( 'output_menu_items', array ($this, 'hook_for_output_menu_items' ), 1, 2 );

        return view ( 'menu/edit_menu.tpl', $data );
    }

    public function setdefault($id) {
        count ( dbupdate ( '{menus}' )->set ( array ('is_default' => 0 ) )->where ( array ('id <>' => $id ) ) );
        count ( dbupdate ( '{menus}' )->set ( array ('is_default' => 1 ) )->where ( array ('id' => $id ) ) );
        Response::redirect ( the_ctr_url ( 'admin', 'menu' ) );
    }

    public function save($id = 0, $item = null) {
        $data = array ('success' => false );
        $form = new MenuForm ();

        if ($form->valid ()) {
            start_tran ();
            $id = $form->save ( $id );
            if ($id) {
                if ($item) {
                    if ($this->save_items ( $item )) {
                        commit_tran ();
                        $data ['success'] = true;
                    } else {
                        rollback_tran ();
                        $data ['msg'] = '无法保存菜单项信息.';
                    }
                } else {
                    commit_tran ();
                    $data ['success'] = true;
                }
            } else {
                rollback_tran ();
                $data ['msg'] = '无法保存菜单信息.';
            }
        } else {
            $formerr = $form->getErrors ();
            $data ['formerr'] = $formerr;
        }
        return new JsonView ( $data );
    }

    public function additem($menu, $type = 'url', $target = '_self') {
        if (empty ( $menu )) {
            return "error:未指定菜单";
        }
        $items = array ();
        if ($type == 'url') {
            $items = $this->add_url_item ( $menu, $target );
        } else if ($type == 'node') {
            $items = $this->add_node_item ( $menu, $target );
        } else {
            return 'error:菜单项类型不正确.';
        }
        if (empty ( $items )) {
            return 'error:未能成功添加菜单项.具体原因请参考日志.';
        }
        $data ['items'] = $items;
        $data ['adding'] = true;
        return view ( 'menu/item.tpl', $data );
    }

    public function delitem($id, $mid) {
        count ( dbdelete ()->from ( '{menuitems}' )->where ( array ('id' => $id ) ) );
        Response::redirect ( the_ctr_url ( 'admin', 'menu/edit/' . $mid ) );
    }
    // 输出菜单项
    function hook_for_output_menu_items($html, $items) {
        if (! $items) {
            return $html;
        }
        foreach ( $items as $item ) {
            $view = view ( 'admin/views/menu/item.tpl', array ('adding' => false, 'items' => array ($item ) ) );
            $itemText = $view->render ();
            $html .= '<li id="menu-item-' . $item ['id'] . '">' . $itemText;
            $subitems = dbselect ( '*' )->from ( '{menuitems}' )->where ( array ('parent' => $item ['id'], 'menu' => $item ['menu'] ) )->asc ( 'sort' );
            if (count ( $subitems ) > 0) {
                $html .= '<ol>';
                $html .= $this->hook_for_output_menu_items ( '', $subitems );
                $html .= '</ol>';
            }
            $html .= '</li>';
        }
        return $html;
    }

    private function save_items($items) {
        if (empty ( $items )) {
            return true;
        }
        foreach ( $items as $item_id => $item ) {
            $rst = dbupdate ( '{menuitems}' )->set ( $item )->where ( array ('id' => $item_id ) );
            $rst = count ( $rst );
            if ($rst === false) {
                return false;
            }
        }
        return true;
    }

    private function add_url_item($menu, $target) {
        $items = array ();
        $item ['menu'] = $menu;
        $item ['name'] = rqst ( 'name' );
        $item ['url'] = rqst ( 'url' );
        $item ['title'] = rqst ( 'title' );
        $item ['type'] = 'url';
        $item ['parent'] = 0;
        $item ['sort'] = 9999;
        $item ['target'] = $target;
        $rst = dbinsert ( $item )->inito ( '{menuitems}' );
        if (count ( $rst )) {
            $item ['id'] = $rst [0];
            $items [] = $item;
        }

        return $items;
    }

    private function add_node_item($menu, $target) {
        $items = array ();
        $ids = safe_ids ( rqst ( 'ids' ), ',', true );
        if (empty ( $ids )) {
            return $items;
        }
        $item ['menu'] = $menu;
        $item ['target'] = $target;
        $item ['type'] = 'node';
        $item ['sort'] = 9999;
        $item ['parent'] = 0;

        foreach ( $ids as $pid ) {
            if (empty ( $pid )) {
                continue;
            }
            $page = dbselect ( '*' )->from ( 'nodes' )->where ( array ('id' => $pid ) );
            $page = $page [0];
            if (! $page) {
                log_warn ( 'the page ' . $pid . ' does not exist.' );
                continue;
            }
            if (dbselect ( 'id' )->from ( '{menuitems}' )->where ( array ('menu' => $menu, 'type' => 'node', 'nid' => $pid ) )->count ( 'id' ) > 0) {
                log_info ( 'the page ' . $pid . ' had already been in the menu:' . $menu );
                continue;
            }
            $item ['nid'] = $pid;
            $item ['name'] = empty ( $page ['title'] ) ? '未命名' : $page ['title'];
            $item ['title'] = $item ['name'];
            $items [] = $item;
        }

        if ($items) {
            $rst = dbinsert ( $items, true )->inito ( '{menuitems}' );
            if (count ( $rst )) {
                $rtn = array ();
                foreach ( $rst as $key => $id ) {
                    $item = $items [$key];
                    $item ['id'] = $id;
                    $rtn [] = $item;
                }
                return $rtn;
            } else {
                return array ();
            }
        }
        return $items;
    }
}