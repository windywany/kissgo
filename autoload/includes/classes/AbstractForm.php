<?php

/**
 * base class for forms
 * @author Guangfeng Ning
 *
 */
abstract class AbstractForm {
    protected $__form_fields = array ();
    protected $__form_data = array ();
    protected $__form_init_data = array ();
    protected $__form_valid = array ();
    protected $__form_validator = null;
    protected $__form_rules = null;

    public function __construct($data = array()) {
        $this->__form_init_data = $data;
        $refObj = new ReflectionObject ( $this );
        $fields = $refObj->getProperties ( ReflectionProperty::IS_PRIVATE );
        $this->__form_validator = new FormValidator ( dirname ( dirname ( __FILE__ ) ) . DS . 'i18n' . DS, get_class ( $this ) );
        foreach ( $fields as $field ) {
            $name = $field->getName ();
            $field->setAccessible ( true );
            $this->__form_fields [$name] = new FormField ( $name, $this, $field->getValue ( $this ) );
        }
    }

    /**
     *
     * get the Filed
     * @param string $name
     * @return FormField
     */
    public function getField($name) {
        if (isset ( $this->__form_fields [$name] )) {
            return $this->__form_fields [$name];
        }
        return null;
    }

    public function removeRlue($name, $rule) {
        $field = $this->getField ( $name );
        if ($field) {
            $field->removeValidate ( $rule );
        }
    }

    public function initValidateRules($reinit = false) {
        if ($this->__form_rules == null || $reinit) {
            $vrules = array ();
            $messages = array ();
            foreach ( $this->__form_fields as $key => $field ) {
                $rule = $field->getValidateRule ();
                list ( $r, $m ) = $this->__form_validator->getRuleClass ( $rule, $this->__form_init_data,$key );
                if ($r) {
                    $vrules [$key] = $r;
                    $messages [$key] = $m;
                }
            }
            $this->__form_rules = array ('rules' => $vrules, 'messages' => $messages );
        }
    }

    public function rules() {
        $this->initValidateRules ();
        return json_encode ( $this->__form_rules );
    }

    public function valid() {
        $this->initValidateRules ();
        $data = $this->toArray ();
        $this->__form_valid = array ();
        foreach ( $this->__form_fields as $key => $field ) {
            $rst = $field->isValid ( $this->__form_validator, $data );
            if (true !== $rst) {
                $this->__form_valid [$key] = $rst;
            }
        }
        return empty ( $this->__form_valid ) ? $data : false;
    }

    public function getErrors() {
        return $this->__form_valid;
    }

    public function getValue($name) {
        if (isset ( $this->__form_data [$name] )) {
            return $this->__form_data [$name];
        } else if (isset ( $this->__form_fields [$name] )) {
            $this->__form_data [$name] = $this->__form_fields [$name]->getValue ();
            return $this->__form_data [$name];
        } else {
            return null;
        }
    }

    public function getBindData($name) {
        $data = array ();
        if (isset ( $this->__form_fields [$name] )) {
            $data = $this->__form_fields [$name]->getBindData ();
        }
        return $data;
    }

    public function toArray() {
        $data = array ();
        foreach ( $this->__form_fields as $name => $field ) {
            $data [$name] = $field->getValue ();
        }
        return $data;
    }

    public function getInitData($name) {
        if (isset ( $this->__form_init_data [$name] )) {
            return $this->__form_init_data [$name];
        }
        return null;
    }
}