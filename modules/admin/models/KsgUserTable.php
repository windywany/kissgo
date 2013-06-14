<?php
/**
 * the users of kissgo
 * @author Leo
 *
 */
class KsgUserTable extends DbTable {
    var $table = 'system_user';
    public function schema() {
        $schema = new DbSchema ( "users" );
        
        $schema->addPrimarykey ( 'uid' );
        
        $schema->addIndex ( 'IDX_STATUS', 'status' );
        
        $schema->addUnique ( 'UIDX_LOGIN_DELETED', array ('deleted', 'login' ) );
        $schema->addUnique ( 'UIDX_EMAIL_DELETE', array ('deleted', 'email' ) );
        
        $schema ['uid'] = array ('type' => 'serial', Idao::UNSIGNED );
        $schema ['deleted'] = array ('type' => 'bool', Idao::CMMT => '1:deleted', Idao::DEFT => false );
        $schema ['login'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 32, Idao::NN );
        $schema ['passwd'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 32, Idao::NN );
        $schema ['username'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 62, Idao::NN );
        $schema ['email'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 64, Idao::NN );
        $schema ['status'] = array ('type' => 'bool', Idao::DEFT => true, Idao::NN, Idao::CMMT => '1:active;0:deactive;' );
        $schema ['reserved'] = array ('type' => 'bool', Idao::DEFT => false, Idao::NN, Idao::CMMT => '1:yes;0:no;' );
        $schema ['last_login_time'] = array ('type' => 'int', Idao::UNSIGNED, Idao::NN, Idao::DEFT => 0 );
        $schema ['last_login_ip'] = array ('type' => Idao::TYPE_VARCHAR, Idao::LENGTH => 45, Idao::DEFT => '' );
        
        return $schema;
    }
}