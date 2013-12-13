<?php

abstract class AbstractForm {
    protected $__form_fields = array ();
    protected $__form_data = array ();
    protected $__form_valid = false;
    protected $__form_validator = null;

    public function __construct() {
        $refObj = new ReflectionObject ( $this );
        $fields = $refObj->getProperties ( ReflectionProperty::IS_PRIVATE );
        $this->__form_validator = new FormValidator ();
        foreach ( $fields as $field ) {
            $name = $field->getName ();
            $field->setAccessible(true);
            $this->__form_fields [$name] = new FormField ( $name, $this, $field->getValue ($this) );
        }
    }
}