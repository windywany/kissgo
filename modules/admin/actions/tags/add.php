<?php
assert_login ();
function do_admin_tags_add_post($req, $res) {
    $rst ['success'] = false;
    $type = rqst ( 'type', 'tag' );
    $tag = rqst ( 'tag' );
    if (empty ( $tag )) {
        $rst ['msg'] = "空的标签";
    } else {
        $tagM = new TagTable ();
        $data ['tag'] = $tag;
        $data ['type'] = $type;
        if ($tagM->exist ( $data )) {
            $data ['msg'] = '标签已经存在';
        } else {
            $tag = $tagM->insert ( $data );
            if ($tag) {
                $rst ['success'] = true;
                $rst ['id'] = $tag ['tag_id'];
            } else {
                $rst ['msg'] = '保存标签时出错：数据库出错.';
            }
        }
    }
    return new JsonView ( $rst );
}