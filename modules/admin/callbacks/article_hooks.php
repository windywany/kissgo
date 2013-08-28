<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 
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