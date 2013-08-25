<?php
/**
 * 
 * @author Leo
 *
 */
class KsgTagTable extends DbTable {
    var $table = 'system_tag';
    public function schema() {
        $schema = new DbSchema ( 'tags' );
        $schema->addPrimarykey ( array ('tag_id' ) );
        $schema->addIndex ( 'IDX_TAG_TYPE', array ('type' ) );
        $schema ['tag_id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN );
        $schema ['tag'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::NN );
        $schema ['slug'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::DEFT => '', Idao::LENGTH => 32, Idao::NN );
        return $schema;
    }
    public function addTagToNode($nid, $tags, $type, $useId = false) {
        if (empty ( $tags ) || empty ( $nid ) || empty ( $type )) {
            return true;
        }
        if (! is_array ( $tags )) {
            $tags = explode ( ',', $tags );
        }
        if (! $useId) {
            //取出所有已经存在的tag,并从$tags数组删除掉已经存在的tag
            $tagsId = $this->query ( 'tag_id,tag' )->where ( array ('tag IN' => $tags, 'type' => $type ) );
            $tagsId = $tagsId->toArray ( 'tag_id', 'tag_id', array (), 'tag', $tags );
        } else {
            foreach ( $tags as $v ) {
                $tagsId [$v] = $v;
            }
            $tags = null;
        }
        // 取出当前node拥有的所有tag
        $ntTable = new KsgNodeTagsTable ();
        $hadTagsId = $ntTable->query ( 'NTG.tag_id', 'NTG' )->where ( array ('node_id' => $nid, 'type' => $type ) )->ljoin ( $this, "TG.tag_id = NTG.tag_id", 'TG' );
        $hadTagsId = $hadTagsId->toArray ( 'tag_id', 'tag_id' );
        //计算将要被删除的(已经有的但是不在要添加的tag里)
        $willBeDeleted = array ();
        foreach ( $hadTagsId as $id ) {
            if (! isset ( $tagsId [$id] )) {
                $willBeDeleted [] = $id;
            }
        }
        if (! empty ( $willBeDeleted )) {
            $ntTable->delete ()->where ( array ('tag_id IN' => $willBeDeleted ) )->count ();
        }
        // 计算将要被添加的(去掉已经有的)        
        foreach ( $hadTagsId as $id ) {
            if (isset ( $tagsId [$id] )) {
                unset ( $tagsId [$id] );
            }
        }
        //将新增的tag添加到tag表
        if (! empty ( $tags )) {
            foreach ( $tags as $tag ) {
                $_tag = $this->insert ( array ('tag' => $tag, 'type' => $type ) );
                if ($_tag) {
                    $id = $_tag ['tag_id'];
                    $tagsId [$id] = $id;
                }
            }
        }
        //添加新增的tag
        if (! empty ( $tagsId )) {
            $tagValue = array ();
            foreach ( $tagsId as $id ) {
                $tagValue [] = '(' . $nid . ',' . $id . ')';
            }
            $tagValue = implode ( ',', $tagValue );
            $sql = 'INSERT INTO ' . $ntTable->getFullTableName () . '(node_id,tag_id) VALUES ' . $tagValue;
            return $ntTable->getDialect ()->exec ( $sql ) > 0;
        }
        return true;
    }
    public function addTags($tag, $type) {
        if (empty ( $tag ) || empty ( $type )) {
            return true;
        }
        if (! is_array ( $tag )) {
            $tag = explode ( ',', $tag );
        }
        $tagsId = $this->query ( 'tag_id,tag' )->where ( array ('tag IN' => $tag, 'type' => $type ) );
        $tagsId = $tagsId->toArray ( 'tag_id', 'tag_id', array (), 'tag', $tag );
        
        if (! empty ( $tag )) {
            $tags = array ('values' => array () );
            foreach ( $tag as $t ) {
                $this->insert ( array ('tag' => $t, 'type' => $type ) );
            }            
        }
        return true;
    }
}