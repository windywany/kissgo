<?php
class CommentDataForm extends DataForm {
    var $subject = array ();
    var $comment = array (FWT_VALIDATOR => array ('required' => '求求你多写点吧.', 'minlength(10)' => '求求你多写点吧.' ) );
    var $author = array ();
    var $url = array (FWT_VALIDATOR => array ('url' => '请填写合法的URL.' ) );
    var $email = array (FWT_VALIDATOR => array ('email' => '请填写合法的EMAIL.' ) );
}