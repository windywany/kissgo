<?php
assert_login ();
function do_admin_tags_get($req, $res, $type = 'tag') {
    $types = array ('tag' => 'Tags', 'flag' => 'Flags', 'keyword' => 'Keywords', 'author' => 'Authors', 'source' => 'Sources' );
    $types = apply_filter ( 'get_enum_type', $types );
    $data = array ('limit' => 50, '_CUR_URL' => murl ( 'admin', 'tags' ) );
    
    if (! isset ( $types [$type] )) {
        $type = 'keyword';
    }
    $data ['type'] = $type;
    $data ['type_text'] = $types [$type];
    $where ['type'] = $type;
    $key = $req ['key'];
    if (! empty ( $key )) {
        $data ['key'] = $key;
        $where ['tag LIKE'] = $key;
    }
    $start = irqst ( 'start', 1 );
    
    $tagM = new KsgTagTable ();
    $tags = $tagM->query ( 'tag_id,tag' )->where ( $where )->limit ( $start, $data ['limit'] );
    $data ['totalTags'] = count ( $tags );
    if ($data ['totalTags'] > 0) {
        $data ['tags'] = $tags;
    } else {
        $data ['tags'] = array ();
    }
    $data ['labels'] = array ('', 'label-success', 'label-warning', 'label-important', 'label-info', 'label-inverse' );
    $data ['tags_types'] = $types;
    return view ( 'admin/views/tags/tags.tpl', $data );
}