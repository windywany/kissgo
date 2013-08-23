<?php
/*
 * admin module ajax callback hooks
 * @author guangfeng Ning <windywany@163.com>
 */
// -----------------------------------------------------------------------
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 
 * ajax validate callback
 * @param Request $req
 */
function do_ajax_validate_check($req) {
    if (! isset ( $req ['__cb'] )) {
        echo 'false';
    } else {
        BaseForm::load_callbacks ();
        $cb = $req ['__cb'];
        $rst = false;
        if (is_callable ( $cb )) {
            $rst = call_user_func_array ( $cb, array (null, $req, null ) );
        }
        if ($rst === true) {
            echo 'true';
        } else if ($rst === false) {
            echo 'false';
        } else {
            echo $rst;
        }
    }
}

/**
 * 
 * 浏览模板文件
 * @param Request $req
 */
function do_ajax_browser_template_files($req) {
    $theme = rqst ( 'theme', THEME );
    $id = rqst ( 'id', '' );
    $path = THEME_PATH . THEME_DIR . DS . $theme . $id;
    $hd = opendir ( $path );
    $dirs = array ();
    $files = array ();
    if ($hd) {
        while ( ($f = readdir ( $hd )) != false ) {
            if (is_dir ( $path . DS . $f ) && $f != '.' && $f != '..' && $f != 'admin') {
                $dirs [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => true );
            }
            if (is_file ( $path . DS . $f ) && preg_match ( '/.+\.tpl$/', $f )) {
                $files [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => false );
            }
        }
        closedir ( $hd );
        ksort ( $dirs );
        ksort ( $files );
        $dirs = $dirs + $files;
        $dirs = array_values ( $dirs );
    }
    echo json_encode ( $dirs );
}
/**
 *
 * browser all template files
 * @param Request $req
 */
function do_ajax_browser_all_template_files($req) {
    $id = rqst ( 'id', '' );
    $path = THEME_PATH . THEME_DIR . DS . $id;
    $hd = opendir ( $path );
    $dirs = array ();
    $files = array ();
    if ($hd) {
        while ( ($f = readdir ( $hd )) != false ) {
            if (is_dir ( $path . DS . $f ) && $f != '.' && $f != '..' && $f != 'admin') {
                $dirs [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => true );
            }
            if (is_file ( $path . DS . $f ) && preg_match ( '/.+\.tpl$/', $f )) {
                $files [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => false );
            }
        }
        closedir ( $hd );
        ksort ( $dirs );
        ksort ( $files );
        $dirs = $dirs + $files;
        $dirs = array_values ( $dirs );
    }
    echo json_encode ( $dirs );
}
/**
 * 
 * browser_menus
 * @param Request $req
 */
function do_ajax_browser_menus($req) {
    $id = rqst ( 'id', '' );
    $rtn = array ();
    if (empty ( $id )) {
        $ksgMenu = new KsgMenuTable ();
        $menus = $ksgMenu->query ()->sort ( 'menu_default' );
        $rtn [0] = array ('id' => '*none', 'name' => 'Home', 'isParent' => true, 'open' => true );
        foreach ( $menus as $menu ) {
            $rtn [0] ['children'] [] = array ('id' => 'm.' . $menu ['menu_name'], 'name' => $menu ['menu_title'], 'cb' => $menu ['menu_title'], 'isParent' => true );
        }
    } else {
        $name = rqst ( 'cb' ) . ' / ';
        $ksgMenuItem = new KsgMenuItemTable ();
        if (is_numeric ( $id )) {
            $where ['up_id'] = $id;
        } else {
            $where ['up_id'] = 0;
            $where ['menu_name'] = str_replace ( 'm.', '', $id );
        }
        $items = $ksgMenuItem->query ( 'menuitem_id,item_name' )->where ( $where )->sort ( 'sort', 'a' );
        foreach ( $items as $item ) {
            $rtn [] = array ('id' => $item ['menuitem_id'], 'name' => $item ['item_name'], 'cb' => $name . $item ['item_name'], 'isParent' => true );
        }
    }
    echo json_encode ( $rtn );
}
/**
 * 
 * browser vfs path
 * @param Request $req
 */
function do_ajax_browser_vfs($req) {
    $id = rqst ( 'id', '' );
    $rtn = array ();
    if (empty ( $id )) {
        if (! isset ( $req ['path'] )) {
            $vfs = new VFSTable ();
            $fs = $vfs->query ( 'fid,url' )->where ( array ('pfid' => 0, 'type' => 'path', 'url <>' => '/' ) )->sort ( 'url', 'a' );
            $rtn [0] = array ('id' => '', 'name' => 'Web Root', 'path' => '/', 'isParent' => true, 'open' => true, 'children' => array () );
            foreach ( $fs as $f ) {
                $rtn [0] ['children'] [] = array ('id' => $f ['fid'], 'name' => $f ['url'], 'path' => $f ['url'], 'isParent' => true );
            }
        }
    } else {
        $path = rqst ( 'path' ) . ' / ';
        $vfs = new VFSTable ();
        $where ['pfid'] = $id;
        $where ['type'] = 'path';
        $items = $vfs->query ( 'fid,url' )->where ( $where )->sort ( 'url', 'a' );
        foreach ( $items as $item ) {
            $rtn [] = array ('id' => $item ['fid'], 'name' => $item ['url'], 'path' => $path . $item ['url'], 'isParent' => true );
        }
    }
    echo json_encode ( $rtn );
}
/**
 * 
 * 读取标签
 * @param Request $req
 */
function do_ajax_tags_autocomplete($req) {
    $q = trim ( rqst ( 'q', '' ), ', ' ); // query term
    $p = irqst ( 'p', 1 ); //page
    $type = rqst ( 't', 'tag' ); // tags type
    $mode = rqst ( 'm', 'n' ); // return value mode 
    $tagTable = new KsgTagTable ();
    $more = true;
    $where = array ('type' => $type );
    if ($mode == 'n') {
        $tags = $tagTable->query ( 'TG.tag_id as id, tag as text', 'TG' );
    } else {
        $tags = $tagTable->query ( 'tag as text', 'TG' );
    }
    if (empty ( $q )) {
        $more = false;
    } else {
        $where ['tag LIKE'] = "%{$q}%";
    }
    $nodeTagTable = new KsgNodeTagsTable ();
    $hots = $nodeTagTable->query ( imtf ( "COUNT(NT.tag_id)", 'total' ), 'NT' )->where ( array ('NT.tag_id' => imtv ( 'TG.tag_id' ) ) );
    $tags->field ( $hots, 'hots' );
    $tags->where ( $where )->limit ( $p, 10 )->sort ( 'hots', 'd' );
    if ($more) {
        $more = $tags->size () == 10;
    }
    if ($mode == 'n') {
        $data = array ('more' => $more, 'results' => $tags->toArray () );
    } else {
        $data = array ('more' => $more, 'results' => array () );
        foreach ( $tags->toArray () as $val ) {
            if ($val ['text'] != $q) {
                $data ['results'] [] = array ('id' => $val ['text'], 'text' => $val ['text'] );
            }
        }
        if (! empty ( $q )) {
            array_unshift ( $data ['results'], array ('id' => $q, 'text' => $q ) );
        }
    }
    echo json_encode ( $data );
}

/**
 * 读取页面
 * @param Request $req
 */
function do_ajax_nodes_autocomplete($req) {
    $q = rqst ( 'q', '' );
    $p = irqst ( 'p', 1 );
    $nodeTable = new KsgNodeTable ();
    $more = true;
    $where = array ('deleted' => 0, 'status' => 'published' );
    $nodes = $nodeTable->query ( 'nid as id, title as text' );
    if (empty ( $q )) {
        $more = false;
    } else {
        $where ['title LIKE'] = "%{$q}%";
    }
    $nodes->where ( $where )->limit ( $p, 10 )->sort ( 'publish_time', 'd' );
    if ($more) {
        $more = $nodes->size () == 10;
    }
    $data = array ('more' => $more, 'results' => $nodes->toArray () );
    echo json_encode ( $data );
}
/**
 * 读取页面
 * @param Request $req
 */
function do_ajax_images_autocomplete($req) {
    $q = rqst ( 'q', '' );
    $p = irqst ( 'p', 1 );
    $attach = new VFSTable ();
    $imgs = $attach->query ( 'url as id,name as text' );
    $where ['type'] = 'image';
    if (empty ( $q )) {
        $more = false;
    } else {
        $where ['name LIKE'] = "%{$q}%";
    }
    $imgs->where ( $where )->limit ( $p, 10 )->sort ( 'create_time', 'd' );
    if ($more) {
        $more = $imgs->size () == 0;
    }
    $data = array ('more' => $more, 'results' => array () );
    $rst = $imgs->toArray ();
    foreach ( $rst as $img ) {
        $img ['t1'] = the_thumbnail_src ( $img ['id'], 80, 60 );
        $img ['t2'] = the_thumbnail_src ( $img ['id'], 260, 180 );
        $data ['results'] [] = $img;
    }
    echo json_encode ( $data );
}
/**
 * 
 * 测试邮件发送功能
 * @param  mixed $params
 */
function do_ajax_test_email($params) {
    KissGo::startSession ();
    $I = whoami ();
    if ($I->isLogin ()) {
        $message = "亲爱的网站管理员：\n\t这是一封测试邮件，如果你能收到，则说明你网站中邮件配置是正确的。\n\n此邮件只用于测试，请不要回复。\n\n请尽情享受KissGO! CMS吧。";
        $email = rqst ( 'email' );
        $rst = sendmail ( $email, "恭喜,你网站的邮件配置通过", $message, null, 'text' );
        if ($rst) {
            $data ['msg'] = '邮件已经发出,请查收.';
        } else {
            $data ['msg'] = '邮件发送失败,详情请查看日志.';
        }
    } else {
        show_error_message ( "你没登录，无权操作." );
    }
    echo json_encode ( $data );
}
// end of do_ajax.php