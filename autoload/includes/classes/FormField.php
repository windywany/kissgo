<?php

class FormField {
    protected $name;
    protected $form;
    protected $request;

    /**
     *
     *
     * @param string $name
     * @param unknown_type $form
     * @param array $options array('bind'=>'func|@func','rules' => array('required(!name)', 'maxlength(10)', 'minlength(1)', 'range(1,5)', 'min(1)', 'max(10)', 'email', 'url', 'callback($scope->aaa)'));
     */
    public function __construct($name, $form, $options) {

    }

    public function getValue($clean = true) {
        return '';
    }
}