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
    $data['_CUR_URL'] = murl('admin','attachs');
    $data ['type_options'] = array_merge ( array ('' => '-请选择-' ), UploadTmpFile::getAttachmentTypes () );
    //bind ( 'attach_icon', array ($this, 'attach_icon' ), 10, 2 );
    //bind ( 'get_attach_actions', array ($this, 'get_attach_actions' ), 10, 2 );
    //bind ( 'get_attachment_bench_options', array ($this, 'get_attachment_bench_options' ) );
    return view ( 'admin/views/attachs/index.tpl', $data );
}
function admin_attach_icon($icon, $item) {
    if ($item ['type'] == 'image') {
        $src = the_thumbnail_src ( $item ['url'], 80, 60 );
        return $src;
    } else if ($item ['type'] == 'doc') {
        if ($item ['ext'] == 'xls' || $item ['ext'] == 'xlsx') {
            return 'images/spreadsheet.png';
        } else if ($item ['ext'] == 'txt') {
            return 'images/text.png';
        } else {
            return 'images/document.png';
        }
    } else if ($item ['type'] == 'zip') {
        return 'images/archive.png';
    } else if ($item ['type'] == 'media') {
        return 'images/video.png';
    }
    return 'images/default.png';
}
function admin_get_attach_actions($actions, $item) {
    $actions .= '<a title="编辑" class="edit-attach"  href="./?Ctlr=EditAttach&aid=' . $item ['attachment_id'] . '"><i class="icon-edit"></i>编辑</a>';
    $actions .= '<a title="删除" onclick="return confirm(\'确定要删除该文件?\');" href="./?Ctlr=DelAttach&aid=' . $item ['attachment_id'] . '"><i class="icon-trash"></i>删除</a>';
    if ($item ['type'] == 'image') {
        $actions .= '<a title="生成缩略图" class="g_thumb" href="./?Ctlr=ThumbPic&aid=' . $item ['attachment_id'] . '"><i class="icon-picture"></i>生成缩略图</a>';
    }
    return $actions;
}
function admin_get_attachment_bench_options($actions) {
    $actions .= '<li><a href="./?Ctlr=ThumbPic&aid=" class="menu-g-thumb"><i class="icon-picture"></i>生成缩略图</a></li>';
    $actions .= '<li><a href="./?Ctlr=DelAttach&aid=" class="menu-del-attach"><i class="icon-trash"></i>永久删除</a></li>';
    return $actions;
}