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

    public function chkusername($value, $data) {
        return 'true';
    }

    public function chkemail($value, $data) {
        return 'true';
    }
}