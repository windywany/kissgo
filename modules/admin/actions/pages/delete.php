<?php
/**
 * 
 * delete the page permanantly
 */
assert_login ();
function do_admin_pages_delete_get() {
    $pids = safe_ids ( rqst ( 'pid' ), ',', true );
    
    $nodeTable = new KsgNodeTable ();
    $tagsTable = new KsgNodeTagsTable ();
    $commentTable = new KsgCommentTable ();
    
    $dialect = $nodeTable->getDialect ();
    
    $nodes = $nodeTable->query ( 'nid,node_type,node_id,vpid,title' )->where ( array ('nid IN' => $pids ) );
    $errors = array ();
    
    foreach ( $nodes as $node ) {
        $rst = false;
        $msg = 'view the log file for details';
        $dialect->beginTransaction ();
        do {
            if (! apply_filter ( 'on_delete_node_' . $node ['node_type'], true, $node )) {
                break;
            }
            if (! apply_filter ( 'on_delete_node', true, $node )) {
                break;
            }
            
            if (! $tagsTable->remove ( array ('node_id' => $node ['nid'] ) )) {
                break;
            }
            
            if (! $commentTable->remove ( array ('node_id' => $node ['nid'] ) )) {
                break;
            }
            
            if (! $nodeTable->remove ( array ('nid' => $node ['nid'] ) )) {
                break;
            }
            $rst = true;
        } while ( 0 );
        
        if ($rst) {
            $dialect->commit ();
        } else {
            $dialect->rollBack ();
            $errors [] = 'Delete page - ' . $node ['title'] . ' unsuccessfully [' . $msg . ']';
        }
    }
    if (! empty ( $errors )) {
        show_page_tip ( 'Oops!<br/>' . implode ( '<br/>', $errors ), 'error' );
    }
    Response::back ();
}