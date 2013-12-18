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
    private $status = array ();
    private $gid = array ('bind'=>'@groups');

    public function chkusername($value, $data) {
        return 'true';
    }

    public function chkemail($value, $data) {
        return 'true';
    }

    public function groups($selected=0){
            $groups = array();
            $rst = dbselect('gid,name')->from('{groups}');
            if(count($rst)){
                foreach ($rst as $group){
                    $groups[$group['gid']] = $group['name'];
                }
            }
            return $groups;
    }
}