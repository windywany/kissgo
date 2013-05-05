<?php
/**
 * 评论
 * @author Leo
 *
 */
class NodeCommentTable extends DbTable {
    var $table = 'node_comment';
    public function schema() {
        $schema = new DbSchema ( 'comments' );
        $schema->addPrimarykey ( array ('id' ) );
        $schema->addIndex ( 'IDX_NODE_ID', array ('node_id' ) );
        $schema->addIndex ( 'IDX_DEL_STATUS', array ('deleted', 'status' ) );
        $schema ['id'] = array ('type' => 'serial', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED );
        $schema ['deleted'] = array ('type' => 'bool', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => false, Idao::CMMT => '是否删除' );
        $schema ['reply_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '回复哪条评论' );
        $schema ['node_id'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '评论哪个页面' );
        $schema ['create_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '创建用户ID' );
        $schema ['create_time'] = array ('type' => 'timestamp', 'extra' => 'normal', Idao::NN, Idao::AUTOINSERT_DATE, Idao::CMMT => '创建时间' );
        $schema ['status'] = array ('type' => 'enum', 'extra' => 'normal', Idao::NN, Idao::ENUM_VALUES => "'new','pass','spam','unpass'", Idao::DEFT => 'new', Idao::CMMT => '审核状态' );
        $schema ['approved_uid'] = array ('type' => 'int', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '审核用户' );
        $schema ['approved_time'] = array ('type' => 'timestamp', 'extra' => 'normal', Idao::NN, Idao::UNSIGNED, Idao::DEFT => 0, Idao::CMMT => '审核时间' );
        $schema ['source_ip'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::NN, Idao::CMMT => '作者的IP' );
        $schema ['author'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::CMMT => '评论作者' );
        $schema ['reply_author'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 64, Idao::CMMT => '此评论回复给作者' );
        $schema ['url'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => '作者的主页' );
        $schema ['email'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::CMMT => '作者的邮箱' );
        $schema ['subject'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 128, Idao::CMMT => '评论主题' );
        $schema ['comment'] = array ('type' => 'text', 'extra' => 'normal', Idao::CMMT => '评论内容' );
        return $schema;
    }
}