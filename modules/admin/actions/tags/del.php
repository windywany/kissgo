<?php
assert_login ();
function do_admin_tags_del_post($req, $res) {
    $data ['success'] = false;
    $tid = safe_ids ( rqst ( 'tid' ), ',', true );
    if (empty ( $tid )) {
        $data ['msg'] = '标签编号为空，无法删除';
    } else {
        $tagM = new TagTable ();
        $driver = $tagM->getDriver ();
        $driver->beginTransaction ();
        $rst = $tagM->remove ( array ('tag_id IN' => $tid ) );
        if ($rst !== false) {
            $wtM = new NodeTagsTable ();
            $rst = $wtM->remove ( array ('tag_id IN' => $tid ) );
            if ($rst !== false) {
                $data ['success'] = true;
                $driver->commit ();
            } else {
                $tagM->rollback ();
                $data ['msg'] = '删除标签出错:' . PdoDriver::$last_error_message;
            }
        } else {
            $driver->rollback ();
            $data ['msg'] = '删除标签出错:' . PdoDriver::$last_error_message;
        }
    }
    
    return new JsonView ( $data );
}