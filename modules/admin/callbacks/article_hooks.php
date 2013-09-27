<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * hook for saving article
 * 
 * @param array $data
 */
function after_save_node_for_plain($data) {
    if ($data ['node_type'] == 'plain' && ! empty ( $data ['node_id'] )) {
        $articleTable = new ArticleTable ();
        $articleTable->update ( array ('nid' => $data ['nid'] ), array ('aid' => $data ['node_id'] ) );
    }
    return $data;
}
/**
 * hook for deleting page of article
 * 
 * @param boolean $rst
 * @param array $node
 */
function on_delete_node_plain($rst, $node) {
    static $aTable = false;
    if (! $aTable) {
        $aTable = new ArticleTable ();
    }
    return $rst && $aTable->update ( array ('nid' => 0 ), array ('aid' => $node ['node_id'] ) );
}