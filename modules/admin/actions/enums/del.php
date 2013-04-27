<?php
assert_login ();
function do_admin_enums_del_post($req, $res) {
    $data ['success'] = false;
    $tid = safe_ids ( rqst ( 'tid' ), ',', true );
    if (empty ( $tid )) {
        $data ['msg'] = '枚举值编号为空，无法删除';
    } else {
        $enumM = new EnumTable ();
        $rst = $enumM->remove ( array ('enum_id IN' => $tid ) );
        if ($rst !== false) {
            $data ['success'] = true;
        } else {
            $data ['msg'] = '删除枚举值出错:' . db_error ();
        }
    }
    return new JsonView ( $data );
}