<?php
assert_login ();
function do_admin_tags_get($req, $res) {
    $data = array ('limit' => 150 );
    $tagM = new TagTable ();
    $where = where ( array ('tag' => array ('like' => 'tag' ) ), $data );
    if (isset ( $req ['flag'] )) {
        $where ['type'] = 1;
        $data ['isTag'] = false;
    } else {
        $where ['type'] = 0;
        $data ['isTag'] = true;
    }
    $start = irqst ( 'start', 1 ); // 分页
    $tags = $tagM->query ( 'tag_id,tag' )->where ( $where )->limit ( $start, $data ['limit'] );
    $data ['totalTags'] = count ( $tags );
    if ($data ['totalTags'] > 0) {
        $data ['tags'] = $tags;
    } else {
        $data ['tags'] = array ();
    }
    $data['_CUR_URL'] = murl('admin','tags');
    $data ['labels'] = array ('', 'label-success', 'label-warning', 'label-important', 'label-info', 'label-inverse' );
    return view ( 'admin/views/tag/tag.tpl', $data );
}