<?php
class KsgNodeTagsTable extends DbTable {
    var $table = 'system_nodetag';
    public function schema() {
        $schema = new DbSchema ( 'node tags' );
        $schema->addPrimarykey ( array ('node_id', 'tag_id' ) );
        $schema ['node_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['tag_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        return $schema;
    }
    public function getNodeFlags($nid) {
        $rst = $this->query ( 'NT.tag_id,tag', 'NT' )->where ( array ('type' => 'flag', 'node_id' => $nid ) );
        $rst->ljoin ( new KsgTagTable (), 'T.tag_id = NT.tag_id', 'T' );
        return $rst->toArray ();
    }
    public function getNodeTags($nid) {
        $rst = $this->query ( 'NT.tag_id,tag', 'NT' )->where ( array ('type' => 'tag', 'node_id' => $nid ) );
        $rst->ljoin ( new KsgTagTable (), 'T.tag_id = NT.tag_id', 'T' );
        return $rst->toArray ();
    }
    public function getHotTags($limit = 10) {
        $where = array ('type' => 0 );
        $tags = $this->query ( 'TG.tag_id, tag', 'TG' );
        $nodeTagTable = new KsgNodeTagsTable ();
        $hots = $nodeTagTable->query ( imtf ( "COUNT(NT.tag_id)", 'total' ), 'NT' )->where ( array ('NT.tag_id' => imtv ( 'TG.tag_id' ) ) );
        $tags->field ( $hots, 'hots' );
        $tags->where ( $where )->limit ( 1, $limit )->sort ( 'hots', 'd' );
        return $tags->toArray ();
    }
}

