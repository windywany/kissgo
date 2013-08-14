<?php
assert_login ();
//评论列表页
function do_admin_comments_get($req, $res, $status = 'new') {
    $data ['_CUR_URL'] = murl ( 'admin', 'comments' );
    $data ['limit'] = 15;
    $nodeCommentTable = new KsgCommentTable ();
    $data ['newTotal'] = $nodeCommentTable->count ( array ('deleted' => 0, 'status' => 'new' ), 'id' );
    $where = array ('CMT.deleted' => 0 );
    $node_id = irqst ( 'nid' );
    if ($node_id) {
        $where ['node_id'] = $node_id;
        $data ['nid'] = $node_id;
    }
    $key = rqst ( 'key' );
    if (! empty ( $key )) {
        $data ['key'] = $key;
        $key = "%{$key}%";
        $where ['&&'] = array ('subject LIKE' => $key, '|comment LIKE' => $key, '|CMT.author LIKE' => $key );
    }
    if ($status == 'trash') {
        $where ['CMT.deleted'] = 1;
    } else {
        $where ['CMT.status'] = $status;
    }
    
    $start = irqst ( 'start', 1 );
    
    $comments = $nodeCommentTable->query ( 'CMT.*,NT.url AS page_url,NT.title AS page_title', 'CMT' )->where ( $where )->limit ( $start, $data ['limit'] );
    $comments->ljoin ( new KsgNodeTable (), 'CMT.node_id = NT.nid', 'NT' )->sort ( 'id', 'd' );
    
    $data ['totalCount'] = count ( $comments );
    $data ['items'] = $comments;
    $data ['status'] = $status;
    
    bind ( 'get_comment_operations', 'hook_for_get_comment_operations', 1, 2 );
    bind ( 'get_comment_bench_options', 'hook_for_get_comment_bench_options', 1, 2 );
    return view ( 'admin/views/comments/list.tpl', $data );
}
//保存或回复评论
function do_admin_comments_post($req, $res) {
    $data ['success'] = false;
    $op = rqst ( 'op', 'reply' );
    $id = irqst ( 'id' );
    if (empty ( $id )) {
        $data ['msg'] = '未指定要回复的评论.';
    } else {
        if ($op == 'reply') { //回复
            $comment ['comment'] = rqst ( 'comment' );
            if (strlen ( $comment ['comment'] ) < 10) {
                $data ['msg'] = '求求你多写一点吧.';
            } else {
                $cmM = new KsgCommentTable ();
                $cmt = $cmM->read ( array ('id' => $id ) );
                if (! $cmt) {
                    $data ['msg'] = '要回复的评论已经不存在.';
                } else {
                    $I = whoami ();
                    $comment ['reply_id'] = $id;
                    $comment ['reply_author'] = $cmt ['author'];
                    $comment ['node_id'] = $cmt ['node_id'];
                    $comment ['author'] = $I->getAccount ();
                    $comment ['email'] = $I->getEmail ();
                    $comment ['status'] = 'pass';
                    $comment ['subject'] = rqst ( 'subject' );
                    $comment ['source_ip'] = $_SERVER ['REMOTE_ADDR'];
                    $rst = $cmM->insert ( $comment );
                    if (! $rst) {
                        $data ['msg'] = '回复评论失败:' . db_error ();
                    } else {
                        $data ['success'] = true;
                        if ($cmt ['status'] != 'pass') {
                            $cmM->update ( array ('status' => 'pass' ), array ('id' => $id ) );
                        }
                    }
                }
            }
        } else { //编辑
            $cmt = new CommentDataForm ();
            $comment = $cmt->validate ();
            if (! $comment) {
                $data ['msg'] = '保存编辑失败:' . $cmt->getError ( "\n" );
            } else {
                $comment ['status'] = 'pass';
                $cmM = new KsgCommentTable ();
                $rst = $cmM->update ( $comment, array ('id' => $id ) );
                if (! $rst) {
                    $data ['msg'] = '保存评论失败:' . db_error ();
                } else {
                    $data ['success'] = true;
                }
            }
        }
    }
    return new JsonView ( $data );
}
//得到针对每个评论的命令
function hook_for_get_comment_operations($opts, $item) {
    static $url = false;
    if (! $url) {
        $url = murl ( 'admin', 'comments' );
    }
    if ($item ['deleted']) {
        $opts .= '<a href="' . $url . '/status?del=0&cid=' . $item ['id'] . '" class="reset-cmt tgre"><i class="icon-share-alt"></i>还原</a>';
        $opts .= '<a onclick="return confirm(\'你确定要删除该评论?\')" href="' . $url . '/delete?cid=' . $item ['id'] . '" class="del-cmt tred"><i class="icon-remove-sign"></i>永久删除</a>';
    } else {
        if ($item ['status'] == 'new' || $item ['status'] == 'unpass') {
            $opts .= '<a href="' . $url . '/status?s=pass&cid=' . $item ['id'] . '" class="pass-cmt tgre"><i class="icon-thumbs-up"></i>批准</a>';
        }
        if ($item ['status'] == 'new' || $item ['status'] == 'pass') {
            $opts .= '<a href="' . $url . '/status?s=unpass&cid=' . $item ['id'] . '" class="unpass-cmt torg"><i class="icon-thumbs-down"></i>驳回</a>';
            $opts .= '<a href="#' . $item ['id'] . '" class="edit-cmt"><i class="icon-edit"></i>编辑</a>';
            $opts .= '<a href="#' . $item ['id'] . '" class="reply-cmt"><i class="icon-comment"></i>回复</a>';
        }
        if ($item ['status'] != 'spam') {
            $opts .= '<a href="' . $url . '/status?s=spam&cid=' . $item ['id'] . '" class="spam-cmt torg"><i class="icon-volume-off"></i>垃圾评论</a>';
            $opts .= '<a href="' . $url . '/status?del=1&cid=' . $item ['id'] . '" class="trash-cmt tred"><i class="icon-trash"></i>移至回收站</a>';
        }
        if ($item ['status'] == 'spam') {
            $opts .= '<a href="' . $url . '/status?s=pass&cid=' . $item ['id'] . '" class="new-cmt tgre"><i class="icon-volume-up"></i>批准</a>';
            $opts .= '<a href="' . $url . '/status?s=unpass&cid=' . $item ['id'] . '" class="unpass-cmt torg"><i class="icon-thumbs-down"></i>驳回</a>';
            $opts .= '<a onclick="return confirm(\'你确定要删除该评论?\')" href="' . $url . '/delete?cid=' . $item ['id'] . '" class="del-cmt tred"><i class="icon-remove-sign"></i>永久删除</a>';
        }
    }
    return $opts;
}
//得到批量处理命令
function hook_for_get_comment_bench_options($opts, $status) {
    static $url = false;
    if (! $url) {
        $url = murl ( 'admin', 'comments' );
    }
    if ($status == 'trash') {
        $opts .= '<li><a href="' . $url . '/status?del=0" class="menu-reset-cmt tgre"><i class="icon-share-alt"></i>还原</a></li>';
        $opts .= '<li><a href="' . $url . '/delete" class="menu-del-cmt tred"><i class="icon-remove-sign"></i>永久删除</a></li>';
    } else if ($status == 'spam') {
        $opts .= '<li><a href="' . $url . '/status?s=pass" class="menu-new-cmt tgre"><i class="icon-volume-up"></i>批准</a></li>';
        $opts .= '<li><a href="' . $url . '/status?s=unpass" class="menu-unpass-cmt torg"><i class="icon-thumbs-down"></i>驳回</a></li>';
        $opts .= '<li><a href="' . $url . '/delete" class="menu-del-cmt tred"><i class="icon-remove-sign"></i>永久删除</a></li>';
    } else {
        if ($status == 'new' || $status == 'unpass') {
            $opts .= '<li><a href="' . $url . '/status?s=pass" class="menu-pass-cmt tgre"><i class="icon-thumbs-up"></i>批准</a></li>';
        }
        if ($status == 'new' || $status == 'pass') {
            $opts .= '<li><a href="' . $url . '/status?s=unpass" class="menu-unpass-cmt torg"><i class="icon-thumbs-down"></i>驳回</a></li>';
        }
        $opts .= '<li><a href="' . $url . '/status?s=spam" class="menu-spam-cmt torg"><i class="icon-fire"></i>垃圾评论</a></li>';
        $opts .= '<li><a href="' . $url . '/status?del=1" class="menu-trash-cmt tred"><i class="icon-trash"></i>移至回收站</a></li>';
    }
    return $opts;
}