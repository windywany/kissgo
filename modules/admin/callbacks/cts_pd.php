<?php
/*
 * @author Ning Guangfeng
 */
//--------------------------------------------------------

/**
 * build the navigator data. 
 * 
 * @see KsgMenuItemTable
 * @see do_admin_menus_get
 * @param array $opts options used to build the navigator data.<br/>
 * <ul>
 * <li>name[optional] - the name of the navigator. if it's blank, the default menu is used.</li>
 * <li>level[optional] - how many levels will be return. default is 1</li>
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
    
    $items->ljoin ( $nodeTable, 'ND.nid = MI.page_id', 'ND' )->sort('sort','a');
    
    $data = array ();
    $opts ['level'] = $opts ['level'] - 1;
    foreach ( $items as $item ) {
        if ($item ['type'] == 'page') {
            $item ['url'] = safe_url($item ['url1']);
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