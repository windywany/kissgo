<?php

/**
 *
 * 用户组
 * @author guangfeng.ning
 *
 */
class UsergroupForm extends AbstractForm {
    private $gid = array ('type' => 'int' );
    private $name = array ('rules' => array ('required' => 'The User Group Name is required', 'callback(@chkname,gid)' => 'The User Group existed.' ) );
    private $note = array ();

    public function chkname($value, $data) {
        $where = array ('name' => $value );
        if ($data ['gid']) {
            $where ['gid <>'] = $data ['gid'];
        }
        $rst = dbselect ( 'gid' )->from ( '{groups}' )->where ( $where );
        if (count ( $rst ) > 0) {
            return __ ( '@admin:The User Group existed.', $value );
        }
        return true;
    }

    public function save($gid = 0) {
        $group = $this->toArray ();
        unset ( $group ['gid'] );
        if ($gid) {
            $rst = dbupdate ( '{groups}' )->set ( $group )->where ( array ('gid' => $gid ) );
        } else {
            $rst = dbinsert ( $group )->inito ( '{groups}' );
        }
        if (count ( $rst ) !== false) {
            if (! $gid) {
                $gid = $rst [0];
            }
            return $gid;
        } else {
            return false;
        }
    }
}