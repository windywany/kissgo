<?php

/**
 *
 * User Form
 * @author guangfeng.ning
 *
 */
class UserForm extends AbstractForm {
    private $id = array ();
    private $username = array ('rules' => array ('required' => 'The username is required.', 'callback(@chkusername,id)' => 'the user already existed.' ) );
    private $display_name = array ();
    private $email = array ('rules' => array ('required' => 'The email is required.', 'email' => 'Please input a valid email.', 'callback(@chkemail,id)' => 'the email already used.' ) );
    private $password = array ('rules' => array ('required' => 'The password is required.', 'minlength(6)' => 'the length at least %s chars' ) );
    private $status = array ('type' => 'int' );
    private $gid = array ('bind' => '@groups', 'type' => 'int' );

    /**
     * callback function for 'status' field to retrieve value
     *
     * @param string $status
     * @return 1 for active 0 for inactive
     */
    public function getStatusValue($status) {
        if ('on' == $status) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * validate username
     *
     * @param string $value
     * @param array $data
     * @return boolean|string
     */
    public function chkusername($value, $data) {
        $where = array ('username' => $value );
        if ($data ['id']) {
            $where ['id <>'] = $data ['id'];
        }
        $rst = dbselect ( 'id' )->from ( '{users}' )->where ( $where );
        if (count ( $rst ) > 0) {
            return false;
        }
        return true;
    }

    /**
     * validate email
     *
     * @param string $value
     * @param array $data
     * @return boolean|string
     */
    public function chkemail($value, $data) {
        $where = array ('email' => $value );
        if ($data ['id']) {
            $where ['id <>'] = $data ['id'];
        }
        $rst = dbselect ( 'id' )->from ( '{users}' )->where ( $where );
        if (count ( $rst ) > 0) {
            return false;
        }
        return true;
    }

    /**
     * save user data inot users table
     *
     * @param int $id
     * @return user id or false
     */
    public function save($id = 0) {
        $user = $this->toArray ();
        if (! empty ( $user ['password'] )) {
            $user ['passwd'] = md5 ( $user ['password'] );
        }
        unset ( $user ['id'], $user ['password'] );
        if ($id) {
            $rst = dbupdate ( '{users}' )->set ( $user )->where ( array ('id' => $id ) );
        } else {
            $rst = dbinsert ( $user )->inito ( '{users}' );
        }
        if (count ( $rst ) !== false) {
            if (! $id) {
                $id = $rst [0];
            }
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 用户组信息
     *
     * @param int $selected
     * @return array
     */
    public function groups($init = array()) {
        if (is_array ( $init )) {
            $groups = $init;
        } else {
            $groups = array ();
        }
        $rst = dbselect ( 'gid,name' )->from ( '{groups}' );
        if (count ( $rst )) {
            foreach ( $rst as $group ) {
                $groups [$group ['gid']] = $group ['name'];
            }
        }
        return $groups;
    }
}