<?php
/**
 * 
 * virtual file system
 * @author guangfeng.ning
 *
 */
class KsgVpathTable extends DbTable {
    var $table = 'system_vpath';
    public function schema() {
        $schema = new DbSchema ( 'virtual file system path' );
        $schema->addPrimarykey ( array ('id' ) );
        $schema->addIndex ( 'IDX_IDX_UPID', array ('upid', 'path' ) );
        $schema->addIndex ( 'IDX_IDX_PATH', array ('paths' ) );
        $schema ['id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['upid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0 );
        $schema ['nid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['name'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 256, Idao::NN );
        $schema ['path'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::NN );
        $schema ['paths'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 1024, Idao::NN );
        return $schema;
    }
    /**
     * get the full path array
     * 
     * @param int $id
     * @param array|null $fpn the null is for the entrence.
     * @return array the array contains the full path nodes.
     */
    public function getFullPathName($id, &$fpn = null) {
        if ($id == 0) {
            return;
        }
        if ($fpn == null) {
            $fullpathname = array ();
            $rst = $this->getPath ( $id );
            if ($rst) {
                $fullpathname [] = $rst;
                $this->getFullPathName ( $rst ['upid'], $fullpathname );
            }
            return array_reverse ( $fullpathname );
        } else {
            $rst = $this->getPath ( $id );
            if ($rst) {
                $fpn [] = $rst;
                $this->getFullPathName ( $rst ['upid'], $fpn );
            }
        }
    }
    private function getPath($id) {
        if ($id == 1) {
            $rst = array ('id' => 1, 'upid' => 0, 'nid' => 0, 'name' => __ ( 'Home' ), 'path' => '/', 'title' => cfg ( 'site_name' ), 'subtitle' => '', 'url' => BASE_URL );
        } else {
            $rst = $this->query ( 'VP.id,VP.upid,VP.nid,VP.name,VP.path,ND.title,ND.subtitle,ND.url', 'VP' )->ljoin ( new KsgNodeTable (), 'ND.nid = VP.nid', 'ND' )->where ( array ('id' => $id ) );
            $rst = $rst [0];
        }
        return $rst;
    }
}