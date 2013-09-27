<?php
/**
 * hook for deleting a node of virtual path
 * 
 * @param boolean $rst
 * @param array $node
 */
function on_delete_node_catalog($rst, $node) {
    static $vpathTable = false;
    if (! $vpathTable) {
        $vpathTable = new KsgVpathTable ();
    }
    if ($rst) {
        if ($vpathTable->exist ( array ('upid' => $node ['node_id'] ) )) { // if this path has children path, we can not delete it.
            return false;
        } else {
            return $vpathTable->remove ( array ('id' => $node ['node_id'] ) );
        }
    }
    return $rst;
}