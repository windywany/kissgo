<?php
/*
 * publish a page
 */
assert_login ();
/**
 *
 *
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
	
	if (empty ( $type )) {
		$type = 'plain';
	}
	$typeM = new KsgNodeTypeTable ();
	$where = array (
			'type' => $type 
	);
	$typeA = $typeM->read ( $where );
	if (empty ( $typeA )) {
		show_page_tip ( '<strong>Oops!</strong>出错啦:无效的页面类型.' );
		Response::back ();
	}
	
	$nodeTable = new KsgNodeTable ();
	$node = $nodeTable->read ( array (
			'node_id' => $pid,
			'node_type' => $type 
	) );
	if (empty ( $node )) {
		$node = array ();
	}
	$data ['widgets'] = apply_filter ( 'get_page_editor_widgets', '', $node );
	
	$ksgTags = new KsgTagTable ();
	$flags = $ksgTags->query ()->where ( array (
			'type' => 'flag' 
	) );
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
 *
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