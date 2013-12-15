<?php

/**
 * Form Field
 * @author Guangfeng Ning
 *
 */
class FormField {
    protected $name;
    protected $form;
    protected $request;
    protected $rules = array ();
    protected $bind;
    protected $type = 'string';
    protected $value;
    protected $init_value;
    protected $required;
    protected $validates;
    protected $default_value = null;

    /**
     *
     * @param string $name
     * @param AbstractForm $form
     * @param array $options
     *            array('field'=>'fieldname','init'=>'aaa|@func','bind'=>'func|@func','rules'
     *            => array('required(!name)',
     *            'maxlength(10)', 'minlength(1)', 'range(1,5)', 'min(1)',
     *            'max(10)', 'email', 'url', 'callback(@aaa)'));
     */
    public function __construct($name, $form, $options) {
        $this->name = $name;
        $this->form = $form;
        if (isset ( $options ['rules'] ) && is_array ( $options ['rules'] )) {
            $this->validates = $options ['rules'];
        }
        if (isset ( $options ['type'] )) {
            $this->type = $options ['type'];
        }
        if (isset ( $options ['bind'] )) {
            $this->bind = $options ['bind'];
        }
        if (isset ( $options ['default'] )) {
            $this->default_value = $options ['default'];
        }
        $this->init_value = $form->getInitData ( $this->name );

        $this->request = Request::getInstance ( true );
    }

    public function getValue() {
        $this->value = $this->request->get ( $this->name, $this->default_value );
        switch ($this->type) {
            case 'int' :
                $this->value = intval ( $this->value );
                break;
            case 'float' :
                $this->value = floatval ( $this->value );
                break;
            default :
                break;
        }
        return $this->value;
    }

    public function getValidateRule() {
        if (is_array ( $this->validates )) {
            foreach ( $this->validates as $rule => $message ) {
                if (is_numeric ( $rule )) {
                    $rule = $message;
                    $message = '';
                }
                $this->addValidate ( $rule, $message );
            }
        }
        return $this->rules;
    }

    public function addValidate($rule, $message) {
        $exp = '';
        if (preg_match ( '#([a-z_][a-z_0-9]+)(\s*\((.*)\))#i', $rule, $rules )) {
            $rule = $rules [1];
            if (isset ( $rules [3] )) {
                $exp = $rules [3];
            }
        }
        $this->rules [$rule] = array ('message' => $message, 'option' => $exp, 'form' => $this->form );
        if ($rule == 'required' && empty ( $exp )) {
            $this->required = true;
        }
    }

    public function removeValidate($rule) {
        unset ( $this->rules [$rule] );
        $this->required = isset ( $this->rules ['required'] ) ? true : false;
    }

    public function getBindData() {
        $data = array ();
        if ($this->bind) {
            if ($this->bind [0] == '@') {
                $func = array ($this->form, substr ( $this->bind, 1 ) );
            } else {
                $func = $this->bind;
            }
            if (is_callable ( $func )) {
                $data = call_user_func_array ( $func, array () );
            }
        }
        return data;
    }

    public function isValid($valiator, $data) {
        if (empty ( $this->rules )) {
            return true;
        } else {
            return $valiator->valid ( $this->value, $data, $this->rules, $this->form );
        }
    }
}