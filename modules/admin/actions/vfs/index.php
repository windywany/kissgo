<?php

function do_admin_vfs_post($req, $res) {
    $I = assert_login ();
    $path = trim ( $req ['path'] );
    $name = $req ['name'];
    $pfid = irqst ( 'pfid', 0 );
    $data = array ('success' => false );
    if (preg_match ( '#^[\d\w_]+$#', $path )) {
        $vfs = new VFSTable ();
        $node = array ('pfid' => $pfid, 'url' => $path, 'type' => 'path' );
        if (! $vfs->exist ( $node )) {
            $node ['create_time'] = time ();
            $node ['create_uid'] = $I ['uid'];
            $node ['name'] = empty ( $name ) ? $path : $name;
            $node ['alt_text'] = $node ['name'];
            $rst = $vfs->insert ( $node );
            if ($rst) {
                $data ['success'] = true;
                $data ['id'] = $rst ['fid'];
            } else {
                $data ['msg'] = db_error ( true );
            }
        } else {
            $data ['msg'] = 'the path "' . $path . '" already exists.';
        }
    } else {
        $data ['msg'] = 'the path "' . $path . '" is invalid.';
    }
    return new JsonView ( $data );
}