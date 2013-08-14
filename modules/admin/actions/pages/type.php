<?php
assert_login ();
/**
 * the index page of node type 
 * @param Request $req
 * @param Response $res
 * @return SmartyView
 */
function do_admin_pages_type_get($req, $res) {
    $data ['_CUR_URL'] = murl ( 'admin', 'pages/type' );
    $data ['limit'] = 20;
    $typeM = new KsgNodeTypeTable ();
    $where = where ( array ('type' => array ('like' => 'type' ), 'name' => array ('like' => 'name' ) ), $data );
    $start = irqst ( 'start', 1 );
    
    $rst = $typeM->query ()->limit ( $start, $data ['limit'] )->where ( $where )->sort ();
    
    $data ['totalTypes'] = count ( $rst );
    
    if ($data ['totalTypes'] > 0) {
        $data ['items'] = $rst;
    } else {
        $data ['items'] = array ();
    }
    
    return view ( 'admin/views/node/type.tpl', $data );
}
/**
 * 修改默认模板文件名
 * @param Request $req
 * @param Response $res
 */
function do_admin_pages_type_post($req, $res) {
    $data ['success'] = false;
    $form = new ModifyNodeTypeForm ();
    if ($form->validate ()) {
        $type = $form->getCleanData ();
        $ntM = new KsgNodeTypeTable ();
        $rst = $ntM->update ( array ('template' => $type ['tpl'] ), array ('id' => $type ['id'] ) );
        if ($rst !== false) {
            $data ['success'] = true;
        } else {
            $data ['msg'] = db_error ();
        }
    } else {
        $data ['msg'] = implode ( "\n", $form->getError () );
    }
    return new JsonView ( $data );
}