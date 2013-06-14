<?php
/**
 * the relationship between role and user
 * @author Leo
 *
 */
class KsgUserRoleTable extends DbTable {
    var $table = 'system_userrole';
    public function schema() {
        $schema = new DbSchema ( "the relationship between role and user" );
        
        $schema->addPrimarykey ( array ('rid', 'uid' ) );
        
        $schema ['rid'] = array ('type' => 'int', Idao::UNSIGNED, Idao::TYPE_EXTRA => Idao::TE_SMALL );
        $schema ['uid'] = array ('type' => 'int', Idao::UNSIGNED );
        $schema ['sort'] = array ('type' => 'int', Idao::UNSIGNED, 'extra' => Idao::TE_SMALL, Idao::NN, Idao::DEFT => 0 );
        
        return $schema;
    }
    public function getGroups($uid) {
        return $this->query ( 'ROLE.*', 'CUR' )->ljoin ( new KsgRoleTable(), 'CUR.rid=ROLE.rid', 'ROLE' )->where ( array ('uid' => $uid ) );
    }
    public function addToGroup($uid, $gids) {
        foreach ( $gids as $gid ) {
            $data ['rid'] = $gid;
            $data ['uid'] = $uid;
            if (! $this->exist ( $data )) {
                $this->insert ( $data );
            }
        }
        return true;
    }
}