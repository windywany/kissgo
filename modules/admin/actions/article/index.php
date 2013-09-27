<?php
assert_login ();

function do_admin_article_get($req, $res, $status = 'draft') {
    $I = whoami ();
    $status_ary = array ('draft', 'published' );
    if (! in_array ( $status, $status_ary )) {
        $status = 'draft';
    }
    $data ['ad'] = $req ['ad'];
    $data ['limit'] = 10;
    $start = irqst ( 'start', 1 );
    $where = where ( array ('ART.title' => array ('like' => array ('name' => 'title' ) ) ), $data );
    if (isset ( $req ['mc'] )) {
        $where ['ART.create_uid'] = $I ['uid'];
        $data ['mc'] = 1;
    }
    $time1 = rqst ( 'time1' );
    $time2 = rqst ( 'time2' );
    if (! empty ( $time1 )) {
        $data ['time1'] = $time1;
        $where ['ART.create_time >='] = strtotime ( $time1 . ' 00:00:00' );
    }
    if (! empty ( $time2 )) {
        $data ['time2'] = $time2;
        $where ['ART.create_time <='] = strtotime ( $time2 . ' 23:59:59' );
    }
    $articleTable = new ArticleTable ();
    if ($status == 'published') {
        $where ['ART.nid >'] = 0;
        $articles = $articleTable->query ( 'ART.*,UC.username AS user_name,UU.username AS update_user_name,ND.title as page_title,ND.url,ND.status', 'ART' );
        $articles->ljoin ( new KsgNodeTable (), "ND.node_type = 'plain' AND ND.node_id = ART.aid", 'ND' );        
    } else {
        $where ['ART.nid'] = 0;
        $articles = $articleTable->query ( 'ART.*,UC.username AS user_name,UU.username AS update_user_name', 'ART' );
    }
    $articles->ljoin ( new KsgUserTable (), 'ART.create_uid = UC.uid', 'UC' );
    $articles->ljoin ( new KsgUserTable (), 'ART.update_uid = UU.uid', 'UU' );
    
    $articles->where ( $where )->limit ( $start, $data ['limit'] )->sort ( 'aid', 'd' );
    $data ['countTotal'] = count ( $articles );
    $data ['items'] = $articles;
    $data ['status'] = $status;
    $data ['editURL'] = murl ( 'admin', 'article/edit' );
    $data ['statusText'] = array ('draft' => 'Drfat', 'published' => 'Published', 'approving' => 'Approving', 'approved' => 'Approved', 'unapproved' => 'Unapproved', 'trash' => 'Trash' );
    bind ( 'get_article_operation', 'hook_for_get_article_operation', 1, 2 );
    return view ( 'admin/views/article/index.tpl', $data );
}

function hook_for_get_article_operation($options, $item) {
    static $url = false;
    if (! $url) {
        $url = murl ( 'admin', 'article' );
    }
    $title = 'data-title="' . $item ['title'] . '"';
    if ($item ['nid']) {
        if ($item ['status'] == 'published') {
            $options .= '<a title="查看" href="' . safe_url ( $item ['url'] ) . '" target="_blank"><i class="icon-eye-open"></i>查看</a>';
        }
        $title = '';
    }
    $options .= '<a title="发布" class="ksg-publish" href="#" data-type="plain" ' . $title . ' data-content="' . $item ['aid'] . '"><i class="icon-check"></i>发布</a>';
    $options .= '<a title="编辑" href="' . $url . '/edit/' . $item ['aid'] . '"><i class="icon-edit"></i>编辑</a>';
    $options .= '<a title="删除" class="tred" href="' . $url . '/del/' . $item ['aid'] . '"><i class="icon-trash"></i>删除</a>';
    return $options;
}