<?php

/**
 * Login Form
 * @author Guangfeng Ning
 *
 */
class LoginForm extends AbstractForm {
    private $formid = array ();
    private $username = array ('rules' => array ('required' ));
    private $passwd = array ('rules' => array ('required','minlength(6)' ));
}
?>