<?php
/*
 * @author Ning Guangfeng
 */
//--------------------------------------------------------


/**
 * build the navigator data. 
 * 
 * @see KsgMenuItemTable
 * @see do_admin_menus_get()
 * @param array $opts options used to build the navigator data.<br/>
 * <ul>
 * <li>name[<i>optional</i>] - the name of the navigator. if it's blank, the default menu is used.</li>
 * <li>level[<i>optional</i>] - how many levels will be return. default is 1</li>
 * </ul>
 * <strong>Sample</strong><br/>
 * <code>
 * {cts from=menu item=m name=default level=2}<br/>
 * your html code here<br/>
 * {/cts}<br/>
 * </code>
 * @param int $upid parent id for recursion
 * @return CtsData key/value array<br/>
 * <ul>
 * <li>id - the menu id</li>
 * <li>name - the menu name</li>
 * <li>url - the menu url</li>
 * <li>target - in which window to open the menu</li>
 * <li>title - string for the A tag title property</li>
 * <li>url1 - ignore this</li>
 * <li>submenus - the submenu of this menu</li>
 * </ul>
 * 
 */
function cts_pd_menu($opts, $upid = 0) {
    static $miTable = false, $mTable = false, $nodeTable = false, $default = false;
    if (! $miTable) {
        $miTable = new KsgMenuItemTable ();
        $mTable = new KsgMenuTable ();
        $nodeTable = new KsgNodeTable ();
        $default = array ('level' => 1 );
    }
    $opts = merge_args ( $opts, $default );
    if ($opts ['level'] == 0) {
        return array ();
    }
    $items = $miTable->query ( 'MI.menuitem_id as id,MI.item_name as name, MI.url,MI.target,MI.title,ND.url as url1,MI.type', 'MI' );
    if ($upid == 0) {
        if (isset ( $opts ['name'] )) {
            $where ['MI.menu_name'] = $opts ['name'];
        } else {
            $where ['menu_default'] = 1;
        }
        $where ['up_id'] = 0;
        $items->ljoin ( $mTable, 'MM.menu_name = MI.menu_name', 'MM' );
    } else {
        $where ['up_id'] = $upid;
    }
    $items->where ( $where );
    
    $items->ljoin ( $nodeTable, 'ND.nid = MI.page_id', 'ND' )->sort ( 'sort', 'a' );
    
    $data = array ();
    $opts ['level'] = $opts ['level'] - 1;
    foreach ( $items as $item ) {
        if ($item ['type'] == 'page') {
            $item ['url'] = safe_url ( $item ['url1'] );
        }
        if ($opts ['level'] > 0) {
            $item ['submenus'] = cts_pd_menu ( $opts, $item ['id'] );
        }
        $data [] = $item;
    }
    if ($upid == 0) {
        return new CtsData ( $data );
    } else {
        return $data;
    }
}
/**
 * retreives pages which satisfies the options.
 * 
 * @param array $options options to retreive the pages<br/>
 * 
 * @return CtsData key/value array<br/>
 * 
 */
function cts_pd_pages($options = array()) {
    global $_CURRENT_NODE;
    
    $page_id = $_CURRENT_NODE ['nid'];
    unset ( $options ['params'] );
    extract ( $options );
    $nodeTable = new KsgNodeTable ();
    // 要查询的字段
    $nodeTable = $nodeTable->query ( 'N1.*' );
    // 条件
    $where ['N1.deleted'] = 0;
    $where ['N1.status'] = 'published';
    
    // 内容模型,属性:types = 'aaa,bbb,ccc'
    if (isset ( $types ) && ! empty ( $types )) {
        $models = explode ( ",", $models );
        if (count ( $models ) > 1) {
            $where ['N1.node_type IN'] = $models;
        } else {
            $where ['N1.node_type'] = $models [0];
        }
    }
    
    // 置顶,属性：ontop = 1
    if (isset ( $ontop ) && $ontop) {
        $where ['N1.ontopto >= '] = date ( 'Y-m-d 00:00:00' );
    }
    // 有插图，属性:figure = 1
    if (isset ( $figure ) && $figure) {
        $where ['N1.figure <>'] = '';
    
    }
    //作者,属性 : author = 'aaa,bbb,ccc'
    if (isset ( $author ) && ! empty ( $author )) {
        $author = explode ( ",", $author );
        if (count ( $author ) > 1) {
            $where ['N1.author IN'] = $author;
        } else {
            $where ['N1.author'] = $author [0];
        }
    }
    //来源，属性：source = 'aaa,bbb,ccc'
    if (isset ( $source ) && ! empty ( $source )) {
        $source = explode ( ",", $source );
        if (count ( $source ) > 1) {
            $where ['N1.source IN'] = $source;
        } else {
            $where ['N1.source'] = $source [0];
        }
    }
    
    // 属性,属性:flags = 'aa,bbb,ccc' or flags = 'aaa|bbb|ccc'
    if (isset ( $flags ) && ! empty ( $flags )) {
        if (strpos ( $flags, "|" ) > 0) { // 且
            $flags = explode ( '|', $flags );
            foreach ( $flags as $flag ) {
                $where .= " AND EXISTS (SELECT WPT.tag_id FROM web_page_tag as WPT LEFT JOIN web_tag AS WT ON(WPT.tag_id = WT.tag_id) WHERE WPT.page_id = WP.page_id AND WT.type = 'flag' AND WT.tag = '" . $flag . "')";
            }
        } else {
            $flags = explode ( ',', $flags );
            $flags = implode ( "','", $flags );
            $where .= " AND EXISTS (SELECT WPT.tag_id FROM web_page_tag as WPT LEFT JOIN web_tag AS WT ON(WPT.tag_id = WT.tag_id) WHERE WPT.page_id = WP.page_id AND WT.type = 'flag' AND WT.tag IN ('" . $flags . "'))";
        }
    }
    // 标签，属性：tags = 'aaaa,bbb,ccc' or tags = 'aaa|bbb|ccc'
    if (isset ( $tags ) && ! empty ( $tags )) {
        if (strpos ( $tags, "|" ) > 0) { // 且
            $tags = explode ( '|', $tags );
            foreach ( $tags as $tag ) {
                $where .= " AND EXISTS (SELECT WPT.tag_id FROM web_page_tag as WPT LEFT JOIN web_tag AS WT ON(WPT.tag_id = WT.tag_id) WHERE WPT.page_id = WP.page_id AND WT.type = 'tag' AND WT.tag = '" . $tag . "')";
            }
        } else { // 或
            $tags = explode ( ',', $tags );
            $tags = implode ( "','", $tags );
            $where .= " AND EXISTS (SELECT WPT.tag_id FROM web_page_tag as WPT LEFT JOIN web_tag AS WT ON(WPT.tag_id = WT.tag_id) WHERE WPT.page_id = WP.page_id AND WT.type = 'tag' AND WT.tag IN ('" . $tags . "'))";
        }
    }
    
    // 排序,属性 sort = 
    if (isset ( $sort ) && ! empty ( $sort )) {
        $sql .= " ORDER BY {$sort} ";
    } else {
        $sql .= " ORDER BY WP.publish_time DESC ";
        $nodeTable->sort();
    }
    
    // 分页
    if (isset ( $start )) {
        $start = intval ( $start );
        $start = $start < 1 ? 1 : $start;
    } else {
        $start = 1;
    }
    if (isset ( $limit )) {
        $limit = intval ( $limit );
        $limit = $limit < 1 ? 10 : $limit;
    } else {
        $limit = 0;
    }
    $nodeTable->where ( $where );
    if ($limit > 0) {
        $nodeTable->limit ( $start - 1, $limit );
    }
    // 查询表
    $nodeTable = apply_filter ( 'alter_cts_pages_query', $nodeTable, $options );
    
    if ($pages) {
        return new CtsDataSet ( $pages, $totalCount, $limit );
    } else {
        return new CtsDataSet ( array (), 0, $limit );
    }
}
/**
 * perform as custom SQL 
 * 
 * @param array $options options to build the SQL<br/>
 * <ul>
 * <li>fields[<i style="color:#f00;">*</i>] - fields to be retreived from database</li>
 * <li>tables[<i style="color:#f00;">*</i>] - from where to retreive data</li>
 * <li>where - condition.</li>
 * <li>order - the sort of the result set</li>
 * <li>limit - how many records will be return, default is 0, retreive all.</li>
 * <li>start - the start position to get data, default is 0</li>
 * <li>having - the advanced SQL option.</li>
 * </ul>
 * @return CtsData key/value<br/>your the keys is your specifidy fields.
 */
function cts_pd_sql($options = array()) {
    static $dialect = false, $builder = false;
    if (! $dialect) {
        $pM = new KsgNodeTable ();
        $dialect = $pM->getDialect ();
        $builder = $dialect->getSqlBuilder ();
    }
    $fields = isset ( $options ['fields'] ) && ! empty ( $options ['fields'] ) ? $options ['fields'] : '*';
    
    $from = isset ( $options ['tables'] ) && ! empty ( $options ['tables'] ) ? $options ['tables'] : false;
    
    $where = isset ( $options ['where'] ) && ! empty ( $options ['where'] ) ? $options ['where'] : false;
    
    $order = isset ( $options ['order'] ) && ! empty ( $options ['order'] ) ? $options ['order'] : false;
    
    $limit = isset ( $options ['limit'] ) && ! is_numeric ( $options ['limit'] ) ? $options ['limit'] : 0;
    
    $start = isset ( $options ['start'] ) && ! is_numeric ( $options ['start'] ) ? $options ['start'] : 0;
    
    $having = isset ( $options ['having'] ) && ! empty ( $options ['having'] ) ? $options ['having'] : false;
    
    if (! $from) {
        return new CtsData ( array () );
    }
    $sql = "SELECT {$fields} FROM {$from}";
    if ($where) {
        $sql .= " WHERE {$where}";
    }
    if ($order) {
        $sql .= " ORDER BY {$order}";
    }
    if ($having) {
        $sql .= " HAVING {$having}";
    }
    if ($limit > 0) {
        $sql = $builder->page_sql ( $sql, $start, $limit );
    }
    try {
        $smt = $dialect->prepare ( $sql );
        if ($smt->execute ()) {
            return new CtsData ( $smt->fetchAll ( PDO::FETCH_ASSOC ) );
        }
    } catch ( PDOException $e ) {
        log_warn ( $e->getMessage () );
    }
    return new CtsData ( array () );
}