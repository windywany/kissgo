<?php
assert_login ();
function do_admin_enums_add_post($req, $res) {
    $rst ['success'] = false;
    $type = rqst ( 'type', 'keyword' );
    $enum = rqst ( 'enum' );
    if (empty ( $enum )) {
        $rst ['msg'] = "空的枚举值";
    } else {
        $enumM = new EnumTable ();
        $data ['enum_value'] = $enum;
        $data ['type'] = $type;
        if ($enumM->exist ( $data )) {
            $data ['msg'] = '枚举值已经存在.';
        } else {
            $enum = $enumM->insert ( $data );
            if ($enum) {
                $rst ['success'] = true;
                $rst ['id'] = $enum ['enum_id'];
            } else {
                $rst ['msg'] = '保存枚举值时出错：' . db_error ();
            }
        }
    }
    return new JsonView ( $rst );
}