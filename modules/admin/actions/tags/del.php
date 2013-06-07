<?php
assert_login ();
function do_admin_tags_del_post($req, $res) {
    $data ['success'] = false;
    $tid = safe_ids ( rqst ( 'tid' ), ',', true );
    if (empty ( $tid )) {
        $data ['msg'] = '标签编号为空，无法删除';
    } else {
        $tagM = new TagTable ();
        $rst = $tagM->remove ( array ('tag_id IN' => $tid ) );
        
        if ($rst !== false) {
            $data ['success'] = true;
            
        } else {
            $data ['msg'] = '删除标签出错:' . db_error ();
        }
    }
    return new JsonView ( $data );
}