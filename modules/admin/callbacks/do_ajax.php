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
        BaseForm::load_callbacks();
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
 * 读取标签
 * @param Request $req
 */
function do_ajax_tags_autocomplete($req) {
    $q = rqst ( 'q', '' );
    $p = irqst ( 'p', 1 );
    $tagTable = new KsgTagTable ();
    $more = true;
    $where = array ('type' => 'tag' );
    $tags = $tagTable->query ( 'TG.tag_id as id, tag as text', 'TG' );
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
        $more = $tags->size () > 0;
    }
    $data = array ('more' => $more, 'results' => $tags->toArray () );
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
        $more = $nodes->size () > 0;
    }
    $data = array ('more' => $more, 'results' => $nodes->toArray () );
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