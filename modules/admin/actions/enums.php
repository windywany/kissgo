<?php
assert_login ();
function do_admin_enums_get($req, $res) {
    $types = array ('tag' => 'Tags', 'flag' => 'Flags', 'keyword' => 'Keywords', 'author' => 'Authors', 'source' => 'Sources' );
    $types = apply_filter ( 'get_enum_type', $types );
    $data = array ('limit' => 50, '_CUR_URL' => murl ( 'admin', 'enums' ) );
    $type = isset ( $req ['type'] ) ? $req ['type'] : 'tag';
    if (! isset ( $types [$type] )) {
        $type = 'keyword';
    }
    $data ['type'] = $type;
    $data ['type_text'] = $types [$type];
    $where ['type'] = $type;
    $key = $req ['key'];
    if (! empty ( $key )) {
        $data ['key'] = $key;
        $where ['enum_value LIKE'] = $key;
    }
    $start = irqst ( 'start', 1 );
    
    $enumM = new EnumTable ();
    $enums = $enumM->query ( 'enum_id,enum_value' )->where ( $where )->limit ( $start, $data ['limit'] );
    $data ['totalEnums'] = count ( $enums );
    if ($data ['totalEnums'] > 0) {
        $data ['enums'] = $enums;
    } else {
        $data ['enums'] = array ();
    }
    $data ['labels'] = array ('', 'label-success', 'label-warning', 'label-important', 'label-info', 'label-inverse' );
    $data ['enums_types'] = $types;
    return view ( 'admin/views/enums/enums.tpl', $data );
}