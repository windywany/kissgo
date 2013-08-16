<?php
/*
 * 页面
 */
assert_login ();
function do_admin_pages_get($req, $res, $status = 'draft') {
    $status_ary = array ('draft', 'published', 'approving', 'approved', 'unapproved', 'published' );
    $data ['_CUR_URL'] = murl ( 'admin', 'pages' );
    $data ['limit'] = 10;
    
    $nodeTable = new KsgNodeTable ();
    $nodeTypeTable = new KsgNodeTypeTable ();
    
    $draftTotal = $nodeTable->count ( array ('deleted' => 0, 'status' => 'draft' ), 'nid' );
    $approvingTotal = $nodeTable->count ( array ('deleted' => 0, 'status' => 'approving' ), 'nid' );
    $where ['ND.deleted'] = 0;
    
    if($status == 'trash'){
        $where ['ND.deleted'] = 1;
    }else{
        $where ['ND.status'] = $status;
    }    
    
    $start = irqst ( 'start', 1 );
    
    $items = $nodeTable->query ( 'ND.*,NT.name AS node_type_name,UC.login AS user_name,UU.login AS update_user_name', 'ND' );
    
    $items->ljoin ( $nodeTypeTable, 'ND.node_type = NT.type', 'NT' );
    $items->ljoin ( new KsgUserTable (), 'ND.create_uid = UC.uid', 'UC' );
    $items->ljoin ( new KsgUserTable (), 'ND.update_uid = UU.uid', 'UU' );
    
    $items->where ( $where )->limit ( $start, $data ['limit'] )->sort ( 'nid', 'd' );
    
    $data ['countTotal'] = count ( $items );
    $data ['items'] = $items;
    
    $data ['draftTotal'] = $draftTotal;
    $data ['approvingTotal'] = $approvingTotal;
    $data ['status'] = $status;
    
    $data ['page_types'] = $nodeTypeTable->query ( 'type,name' )->where ( array ('creatable' => 1 ) )->toArray ( 'type', 'name', array ('' => '-页面类型-' ) );
    $tagM = new KsgTagTable ();
    $data ['flags'] = $tagM->query ( 'tag_id,tag' )->where ( array ('type' => 'flag' ) )->toArray ( 'tag_id', 'tag', array ('' => '-页面属性-' ) );
    
    $nk = new NodeHooks ();
    bind ( 'get_page_operation', array ($nk, 'get_page_operation' ), 1, 2 );
    bind ( 'get_page_bench_options', array ($nk, 'get_page_bench_options' ), 1, 2 );
    bind ( 'show_node_flags', array ($nk, 'show_node_flags' ), 1, 2 );
    bind ( 'show_node_tags', array ($nk, 'show_node_tags' ), 1, 2 );
    
    return view ( 'admin/views/node/list.tpl', $data );
}

/**
 * node hooks
 * @author Leo
 *
 */
class NodeHooks {
    private $wptM;
    private $url;
    private $edit_url;
    private $delete_url;
    public function __construct() {
        $this->wptM = new KsgNodeTagsTable ();
        $this->url = murl ( 'admin', 'pages/flow' );
        $this->edit_url = murl ( 'admin', 'pages/publish/' );
        $this->delete_url = murl ( 'admin', 'pages/delete' );
    }
    public function get_page_operation($options, $item) {
        $status = $item ['status'];
        if ($item ['deleted']) {
            $options .= '<a title="还原" class="reset tgre" href="' . $this->url . '?del=0&pid=' . $item ['nid'] . '"><i class="icon-share-alt"></i>还原</a>';
            $options .= '<a title="永久删除" onclick="return confirm(\'网页删除后不可恢复,你确定要删除该网页吗?\')" class="delete tred" href="' . $this->delete_url . '?pid=' . $item ['nid'] . '"><i class="icon-remove-sign"></i>永久删除</a>';
        } else {
            if ($status == 'draft') {
                $options .= '<a title="编辑" class="edit-page" href="#" data-type="'.$item ['node_type'].'" data-content="' . $item ['nid'] . '" id="edit-page-' . $item ['nid'] . '"><i class="icon-edit"></i>编辑</a>';
                //if (icando ( 'publish', 'page' )) {
                $options .= '<a title="发布" class="published tgre" href="' . $this->url . '?s=published&pid=' . $item ['nid'] . '"><i class="icon-check"></i>发布</a>';
                //}
                $options .= '<a title="提交审核" class="approving" href="' . $this->url . '?s=approving&pid=' . $item ['nid'] . '"><i class="icon-filter"></i>提交审核</a>';
                $options .= '<a title="移至回收站" class="trash tred" href="' . $this->url . '?del=1&pid=' . $item ['nid'] . '"><i class="icon-trash"></i>移至回收站</a>';
            }
            if ($status == 'approving') {
                $options .= '<a title="批准" class="approved tgre" href="' . $this->url . '?s=approved&pid=' . $item ['nid'] . '"><i class="icon-thumbs-up"></i>批准</a>';
                $options .= '<a title="驳回" class="unapproved torg" href="' . $this->url . '?s=unapproved&pid=' . $item ['nid'] . '"><i class="icon-thumbs-down"></i>驳回</a>';
            }
            if ($status == 'approved') {
                $options .= '<a title="编辑" class="edit-page" href="#" data-type="'.$item ['node_type'].'" data-content="' . $item ['nid'] . '" id="edit-page-' . $item ['nid'] . '"><i class="icon-edit"></i>编辑</a>';
                $options .= '<a title="发布" class="published tgre" href="' . $this->url . '?s=published&pid=' . $item ['nid'] . '"><i class="icon-check"></i>发布</a>';
                $options .= '<a title="移至草稿箱" class="draft torg" href="' . $this->url . '?s=draft&pid=' . $item ['nid'] . '"><i class="icon-share"></i>移至草稿箱</a>';
                $options .= '<a title="移至回收站" class="trash tred" href="' . $this->url . '?del=1&pid=' . $item ['nid'] . '"><i class="icon-trash"></i>移至回收站</a>';
            }
            if ($status == 'unapproved') {
                $options .= '<a title="编辑" class="edit-page" href="#" data-type="'.$item ['node_type'].'" data-content="' . $item ['nid'] . '" id="edit-page-' . $item ['nid'] . '"><i class="icon-edit"></i>编辑</a>';
                $options .= '<a title="移至草稿箱" class="draft torg" href="' . $this->url . '?s=draft&pid=' . $item ['nid'] . '"><i class="icon-share"></i>移至草稿箱</a>';
                $options .= '<a title="移至回收站" class="trash tred" href="' . $this->url . '?del=1&pid=' . $item ['nid'] . '"><i class="icon-trash"></i>移至回收站</a>';
            }
            if ($status == 'published') {
                $options .= '<a title="查看" href="' . safe_url ( $item ) . '" target="_blank"><i class="icon-eye-open"></i>查看</a>';
                $options .= '<a title="编辑" class="edit-page" href="#" data-type="'.$item ['node_type'].'" data-content="' . $item ['nid'] . '" id="edit-page-' . $item ['nid'] . '"><i class="icon-edit"></i>编辑</a>';
                $options .= '<a title="移至草稿箱" class="draft torg" href="' . $this->url . '?s=draft&pid=' . $item ['nid'] . '"><i class="icon-share"></i>移至草稿箱</a>';
                $options .= '<a title="移至回收站" class="trash tred" href="' . $this->url . '?del=1&pid=' . $item ['nid'] . '"><i class="icon-trash"></i>移至回收站</a>';
            }
        }
        return $options;
    }
    // 页面批量操作
    public function get_page_bench_options($options, $status) {
        if ($status == 'deleted') {
            $options .= '<li><a title="还原" class="reset" href="' . $this->url . '?del=0"><i class="icon-share-alt"></i>还原</a></li>';
            $options .= '<li><a title="永久删除" class="delete" href="' . $this->delete_url . '"><i class="icon-remove-sign"></i>永久删除</a></li>';
        }
        if ($status == 'draft') {
            $options .= '<li><a title="提交审核" class="approving" href="' . $this->url . '?s=approving"><i class="icon-filter"></i>提交审核</a></li>';
            $options .= '<li><a title="移至回收站" class="trash" href="' . $this->url . '?del=1"><i class="icon-trash"></i>移至回收站</a></li>';
        }
        if ($status == 'approving') {
            $options .= '<li><a title="批准" class="approved" href="' . $this->url . '?s=approved"><i class="icon-thumbs-up"></i>批准</a></li>';
            $options .= '<li><a title="驳回" class="unapproved" href="' . $this->url . '?s=unapproved"><i class="icon-thumbs-down"></i>驳回</a></li>';
        }
        if ($status == 'approved') {
            $options .= '<li><a title="发布" class="published" href="' . $this->url . '?s=published"><i class="icon-check"></i>发布</a></li>';
            $options .= '<li><a title="移至草稿箱" class="draft" href="' . $this->url . '?s=draft"><i class="icon-share"></i>移至草稿箱</a></li>';
            $options .= '<li><a title="移至回收站" class="trash" href="' . $this->url . '?del=1"><i class="icon-trash"></i>移至回收站</a></li>';
        }
        if ($status == 'unapproved') {
            $options .= '<li><a title="移至草稿箱" class="draft" href="' . $this->url . '?s=draft"><i class="icon-share"></i>移至草稿箱</a></li>';
            $options .= '<li><a title="移至回收站" class="trash" href="' . $this->url . '?del=1"><i class="icon-trash"></i>移至回收站</a></li>';
        }
        if ($status == 'published') {
            $options .= '<li><a title="移至草稿箱" class="draft" href="' . $this->url . '?s=draft"><i class="icon-share"></i>移至草稿箱</a></li>';
            $options .= '<li><a title="移至回收站" class="trash" href="' . $this->url . '?del=1"><i class="icon-trash"></i>移至回收站</a></li>';
        }
        
        return $options;
    }
    // 显示页面属性
    public function show_node_flags($flags, $item) {
        $fls = $this->wptM->getNodeFlags ( $item ['nid'] );
        if ($fls) {
            foreach ( $fls as $fname ) {
                $flags .= "<span class=\"label label-info\">{$fname['tag']}</span>&nbsp;";
            }
        }
        return $flags;
    }
    // 显示页面属性
    public function show_node_tags($flags, $item) {
        $fls = $this->wptM->getNodeTags ( $item ['nid'] );
        if ($fls) {
            foreach ( $fls as $fname ) {
                $flags .= "<span class=\"label label-info pull-left mg-r5\">{$fname['tag']}</span>";
            }
        }
        return $flags;
    }
}