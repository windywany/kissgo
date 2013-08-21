<?php
/*
 * publish a page
 */
assert_login ();
/**
 *
 *
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
	if (empty ( $pid )) {
		show_page_tip ( '<strong>Oops!</strong>出错啦:无效的页面编号.' );
		Response::back ();
	}
	if (empty ( $type )) {
		$type = 'article';
	}
	$nodeTable = new KsgNodeTable ();
	$node = $nodeTable->read ( array (
			'nid' => $pid 
	) );
	if (empty ( $node )) {
		show_page_tip ( '<strong>Oops!</strong>出错啦:页面不存在.' );
		Response::back ();
	}
	$data ['node'] = $node;
	$data ['type'] = $type;
	$data ['node_id'] = $pid;
	$data ['widgets'] = apply_filter ( 'get_page_editor_widgets', '', $node );
	$data ['hideNavi'] = true;
	return view ( 'admin/views/node/editor/editor.tpl', $data );
}
/**
 *
 *
 *
 * publish page
 *
 * @param Request $req        	
 * @param Response $res        	
 * @param int $pid        	
 * @param string $type        	
 */
function do_admin_pages_publish_post($req, $res, $type = '', $pid = 0) {
    return "ok";
}
// end of admin/actions/pages/publish.php