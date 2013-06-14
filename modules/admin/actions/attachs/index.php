<?php
/*
 * 附件 
 */
assert_login ();
/**
 * 
 * @param unknown_type $req
 * @param unknown_type $res
 * @return SmartyView
 */
function do_admin_attachs_get($req, $res) {    
    $data ['limit'] = 15;
    $data ['_CUR_URL'] = murl ( 'admin', 'attachs' );
    $data ['type_options'] = array_merge ( array ('' => '-请选择-' ), UploadTmpFile::getAttachmentTypes () );
    
    $start = rqst ( 'start', 1 ); // 分页
    $where = where ( array ('name' => array ('like' => 'name' ), 'type' ), $data );
    $time1 = rqst ( 'time1' );
    $time2 = rqst ( 'time2' );
    if (! empty ( $time1 )) {
        $data ['time1'] = $time1;
        $where ['create_time >='] = strtotime ( $time1 . ' 00:00:00' );
    }
    if (! empty ( $time2 )) {
        $data ['time2'] = $time2;
        $where ['create_time <='] = strtotime ( $time2 . ' 23:59:59' );
    }
    
    $attModel = new KsgAttachmentTable ();
    $query = $attModel->query ( "AMT.*,U.login AS author", 'AMT' );
    
    $query->ljoin ( new KsgUserTable(), "AMT.create_uid  = U.uid" ,'U');
    
    $query->sort ( 'create_time' );
    
    $query->where ( $where )->limit ( $start, $data ['limit'] );
    
    $data ['total'] = count ( $query );
    $data ['items'] = array ();
    if ($data ['total'] > 0) {
        $data ['items'] = $query;
    }
    
    bind ( 'attach_icon', 'admin_attach_icon', 10, 2 );
    bind ( 'get_attach_actions', 'admin_get_attach_actions', 10, 2 );
    bind ( 'get_attachment_bench_options', 'admin_get_attachment_bench_options' );
    return view ( 'admin/views/attachs/index.tpl', $data );
}
function admin_attach_icon($icon, $item) {
    if ($item ['type'] == 'image') {
        $src = the_thumbnail_src ( $item ['url'], 80, 60 );
        return $src;
    } else if ($item ['type'] == 'doc') {
        if ($item ['ext'] == 'xls' || $item ['ext'] == 'xlsx') {
            return BASE_URL . STATIC_DIR . '/images/icons/spreadsheet.png';
        } else if ($item ['ext'] == 'txt') {
            return BASE_URL . STATIC_DIR . '/images/icons/text.png';
        } else {
            return BASE_URL . STATIC_DIR . '/images/icons/document.png';
        }
    } else if ($item ['type'] == 'zip') {
        return BASE_URL . STATIC_DIR . '/images/icons/archive.png';
    } else if ($item ['type'] == 'media') {
        return BASE_URL . STATIC_DIR . '/images/icons/video.png';
    }
    return BASE_URL . STATIC_DIR . '/images/icons/default.png';
}
function admin_get_attach_actions($actions, $item) {
    static $urls = false;
    if (! $urls) {
        $urls ['edit'] = murl ( 'admin', 'attachs/edit' );
        $urls ['delete'] = murl ( 'admin', 'attachs/delete' );
        $urls ['thumb'] = murl ( 'admin', 'attachs/thumb' );
    }
    $actions .= '<a title="编辑" class="edit-attach"  href="' . $urls ['edit'] . '?aid=' . $item ['attachment_id'] . '"><i class="icon-edit"></i>编辑</a>';
    $actions .= '<a title="删除" onclick="return confirm(\'确定要删除该文件?\');" href="' . $urls ['delete'] . '?aid=' . $item ['attachment_id'] . '"><i class="icon-trash"></i>删除</a>';
    if ($item ['type'] == 'image') {
        $actions .= '<a title="生成缩略图" class="g_thumb" href="' . $urls ['thumb'] . '?aid=' . $item ['attachment_id'] . '"><i class="icon-picture"></i>生成缩略图</a>';
    }
    return $actions;
}
function admin_get_attachment_bench_options($actions) {
    $actions .= '<li><a href="'.murl ( 'admin', 'attachs/thumb' ).'?aid=" class="menu-g-thumb"><i class="icon-picture"></i>生成缩略图</a></li>';
    $actions .= '<li><a href="'.murl ( 'admin', 'attachs/delete' ).'?aid=" class="menu-del-attach"><i class="icon-trash"></i>永久删除</a></li>';
    return $actions;
}