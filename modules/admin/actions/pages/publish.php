<?php
/*
 * publish a page
 */
assert_login ();
/**
 * 
 * open the publish page
 *
 * @param Request $req        	
 * @param Response $res        	
 * @param int $pid        	
 * @param string $type        	
 */
function do_admin_pages_publish_get($req, $res, $type = '', $pid = 0) {
    $data ['_CUR_URL'] = murl ( 'admin', 'pages/publish' );
    
    if (empty ( $type )) {
        $type = 'plain';
    }
    $typeM = new KsgNodeTypeTable ();
    $where = array ('type' => $type );
    $typeA = $typeM->read ( $where );
    if (empty ( $typeA )) {
        show_page_tip ( '<strong>Oops!</strong>出错啦:无效的页面类型.' );
        Response::back ();
    }
    if (empty ( $pid )) {
        $node = new FrontPage ();
        $node = $node->toArray ( false, true );
    } else {
        $node = FrontPage::initWithNodeType ( $type, $pid );
        if (! empty ( $node )) {
            $node = $node->toArray ( false, true );
        } else {
            $node = new FrontPage ();
            $node = $node->toArray ( false, true );
        }
    }
    $node ['tags'] = empty ( $node ['tags'] ) ? '' : implode ( ',', $node ['tags'] );
    $data ['widgets'] = apply_filter ( 'get_page_editor_widgets', '', $node );
    $ksgTags = new KsgTagTable ();
    $flags = $ksgTags->query ()->where ( array ('type' => 'flag' ) );
    $data ['node'] = $node;
    $data ['type'] = $type;
    $data ['type_name'] = $typeA ['name'];
    $data ['node_id'] = $pid;
    $data ['flags'] = $flags;
    $data ['hideNavi'] = true;
    return view ( 'admin/views/node/editor/editor.tpl', $data );
}
/**
 *
 * publish page
 *
 * @param Request $req        	
 * @param Response $res        	
 * @param int $pid        	
 * @param string $type        	
 */
function do_admin_pages_publish_post($req, $res) {
    $data ['success'] = false;
    $nodeForm = new NodeForm ();
    $node = $nodeForm->validate ();
    if ($node === false) {
        $data ['msg'] = $nodeForm->getError ( '<br/>' );
    } else {
        if ($node ['commentable'] == 'on') {
            $node ['commentable'] = 1;
        } else {
            $node ['commentable'] = 0;
        }
        if ($node ['custome_tpl_chk'] != 'on') {
            $node ['template'] = '';
        }
        if (! empty ( $node ['url'] )) {
            $node ['url_slug'] = md5 ( $node ['url'] );
        }
        if (empty ( $node ['ontopto'] )) {
            $node ['ontopto'] = null;
        }
        $page = new FrontPage ( $node );
        $rst = $page->save ();
        if ($rst) {
            $data ['success'] = true;
        } else {
            $data ['msg'] = '保存页面出错';
        }
    }
    return new JsonView ( $data );
}
// end of admin/actions/pages/publish.php