<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo.core
 *
 * $Id$
 */
define ('FWT_NAME', 'name');
define ('FWT_WIDGET', 'widget');
define ('FWT_LABEL', 'label');
define ('FWT_BIND', 'bind');
define ('FWT_ID', 'id');
define ('FWT_FIELD', 'field');
define ('FWT_VALIDATOR', 'validator');
define ('FWT_TIP', 'tip');
define ('FWT_SEARCH', 'search');
define ('FWT_OPTIONS', 'options');
define ('FWT_INITIAL', 'initial');
/**
 * 抽象表单
 * Simple Code:
 * <code>
 * class myForm extends TableForm {
 *  var $name = array('label' => '姓名', 'bind' => 'abc', 'search' => '<>', 'field' => 'name1', 'validator' => '');
 *  var $age = array('label' => '年龄', 'validator' => array('required', 'maxlength(10)', 'minlength(1)', 'range(1,5)', 'min(1)', 'max(10)', 'email', 'url', 'callback($scope->aaa)'));
 * }
 * </code>
 */
abstract class BaseForm implements ArrayAccess{
    private $__properties__ = array();

    public function __construct($data = array(), $title = '', $options = array()) {
        $this->initialize($data);
        $this->title = $title;
        $this->validator = new BaseValidator();
        if (isset ($options ['id'])) {
            $this->id = $options ['id'];
        } else {
            $this->id = get_class($this);
        }
        $this->options = $options;
    }

    public function getCleanData($widget = null, $default = '') {
        static $clean_data = false;
        if (!$clean_data) {
            $request = Request::getInstance(true);
            $clean_data = array();
            foreach ($this->widgets as $name => $widget_object) {
                $key = $this->{$name} [FWT_FIELD];
                $clean_data [$key] = $widget_object->getValue($request);
            }
            $this->__properties__['__cleandata'] = $clean_data;
        }
        if (!empty ($widget)) {
            if (is_array($widget)) {
                $key = $widget [FWT_FIELD];
                $default = $widget [FWT_INITIAL];
            } else {
                $key = $widget;
            }
            if (isset ($clean_data [$key])) {
                return $clean_data [$key];
            } else {
                return $default;
            }
        }
        return $clean_data;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getInitialData() {
        return $this->data;
    }

    public function getSearchCondition() {
    }

    /**
     *
     *
     * @param IValidator $validator
     * @return IValidator
     */
    public function useValidator($validator = null) {
        if ($validator instanceof IValidator) {
            return $this->validator = $validator;
        }
        return $this->validator;
    }

    /**
     * @param string|null $name
     * @param null|string $component
     * @internal var FormWidget $widget
     * @return string
     */
    public function render($name = null, $component = null) {
        $body = '';
        if (!is_null($name) && !is_null($component)) {
            if (isset ($this->widgets [$name])) {
                $widget = $this->widgets [$name];
                switch ($component) {
                    case 'label' :
                        $body = $widget->getLabelComponent();
                        break;
                    case 'widget' :
                        $body = $widget->getWidgetComponent();
                        break;
                    case 'tip' :
                        $body = $widget->getTipComponent();
                        break;
                    case 'error' :
                        $body = $widget->error;
                        break;
                    case 'value':
                        $body = $widget->getValue();
                        break;
                    case 'validate':
                    	$body = $widget->getValidate();
                    	break;
                    default :
                        $body = str_replace(array('{$error_class}', '{$label}', '{$widget}', '{$tip}'), array($widget->valid ? '' : 'error', $widget->getLabelComponent(), $widget->getWidgetComponent(), $widget->getTipComponent()), $this->getFormItemWrapper());
                        break;
                }
                return $body;
            } else {
                return '';
            }
        } else if ($component == null) {
            switch ($name) {
                case 'errors':
                	if(!empty($this->errors)){
                    	$body = '<p class="form-error">' . implode('</p><p class="form-error">', $this->errors) . '</p>';
                	}
                    break;
                case 'options':                    
                    if ($this->options) {
                    	$body = html_tag_properties($this->options);
                    }                    
                    break;
                default:
                    break;
            }
            return $body;
        } else {
            $head = $this->getFormHead();
            $item_wrapper = $this->getFormItemWrapper();
            $foot = $this->getFormFoot();
            $body = '';
            foreach ($this->widgets as $widget) {
                $body .= str_replace(array('{$error_class}', '{$label}', '{$widget}', '{$tip}'), array($widget->valid ? '' : 'error', $widget->getLabelComponent(), $widget->getWidgetComponent(), $widget->getTipComponent()), $item_wrapper) . "\n";
            }
            return $head . "\n" . $body . $foot;
        }
    }

    public function valid($scope = null) {
        $clean_data = $this->getCleanData();
        $errors = array();
        $scope = is_object($scope) ? $scope : $this;
        foreach ($this->widgets as $widget) {
            if (!$widget->valid($clean_data, $scope)) {
                $errors [] = $widget->error;
            }
        }
        $this->errors = $errors;
        return count($errors) > 0 ? false : $clean_data;
    }

    protected function initialize($data) {
        $widgets = get_object_vars($this);
        $this->data = $data;
        if (!empty ($widgets)) {
            foreach ($widgets as $widget_name => $widget) {
                if (preg_match('#__.+#', $widget_name)) {
                    continue;
                }
                $widget_class = isset ($widget [FWT_WIDGET]) && !empty ($widget [FWT_WIDGET]) ? $widget [FWT_WIDGET] : 'Text';
                $widget_class = ucfirst($widget_class) . 'Widget';
                if (!class_exists($widget_class) || !is_subclass_of($widget_class, 'FormWidget')) {
                    continue;
                }

                $key = isset ($widget [FWT_FIELD]) && !empty ($widget [FWT_FIELD]) ? $widget [FWT_FIELD] : $widget_name;
                if (isset ($widget [FWT_BIND]) && is_array($widget [FWT_BIND]) && count($widget [FWT_BIND]) == 1) {
                    $widget [FWT_BIND] = array_merge(array($this), $widget [FWT_BIND]);
                }
                if (isset ($widget [FWT_SEARCH])) {
                    $this->addSearch($widget_name, $widget [FWT_SEARCH]);
                }
                $widget [FWT_NAME] = $widget_name;
                if (!isset ($widget [FWT_LABEL])) {
                    $widget [FWT_LABEL] = ucfirst($widget_name);
                }
                $widget[FWT_LABEL] = __($widget[FWT_LABEL]);
                $this->{$widget_name} [FWT_NAME] = $widget_name;
                $this->{$widget_name} [FWT_FIELD] = $key;
                $widget_object = new $widget_class ($widget, $data [$key], $this);
                $this->addWidget($widget_name, $widget_object);
            }
        }
    }

    public function __set($name, $value) {
        $this->__properties__ [$name] = $value;
    }

    public function addWidget($widget_name, $widget) {
        $this->__properties__ ['widgets'] [$widget_name] = $widget;
    }

    public function addSearch($widget_name, $search) {
        $this->__properties__ ['searches'] [$widget_name] = $search;
    }

    public function isValid() {
        return count($this->errors) > 0 ? false : true;
    }

    public function __get($name) {
        if (isset ($this->__properties__ [$name])) {
            return $this->__properties__ [$name];
        }
        return null;
    }
	public function offsetExists($offset) {
		return isset($this->__properties__['__cleandata'][$offset]);		
	}	
	public function offsetGet($offset) {
		if(isset($this->__properties__['__cleandata'][$offset])){
			return $this->__properties__['__cleandata'][$offset];
		}else if(isset($this->{$offset}) && isset($this->{$offset}[FWT_INITIAL])){
			return $this->{$offset}[FWT_INITIAL];
		}
		return '';
	}	
	public function offsetSet($offset, $value) {		
		
	}	
	public function offsetUnset($offset) {		
	}
	protected abstract function getFormHead();

    protected abstract function getFormItemWrapper();

    protected abstract function getFormFoot();
}

/**
 *
 * Form Validator
 * @author guangfeng.ning
 *
 */
interface IValidator {
    public function yield(&$properties, $rules);

    public function valid($value, $data, $rules, $scope);
}

/**
 * Widget
 */
abstract class FormWidget {
    protected $is_valid = true;
    protected $error = null;
    protected $option = null;
    protected $form = null;
    protected $value = null;
    protected $validates = array();
    protected $required = false;

    public function __construct($option, $value = '', $form) {
        $this->option = $option;
        $this->form = $form;
        $this->value = $value;
        if (!isset ($option [FWT_ID]) || empty ($option [FWT_ID])) {
            $this->option [FWT_ID] = $option [FWT_NAME];
        }
        if (isset ($this->option [FWT_VALIDATOR]) && is_array($this->option [FWT_VALIDATOR])) {
            foreach ($this->option [FWT_VALIDATOR] as $rule => $message) {
                if (is_numeric($rule)) {
                    $rule = $message;
                    $message = '';
                }
                $exp = '';
                if (preg_match('#([a-z_][a-z_0-9]+)(\s*\((.*)\))#', $rule, $rules)) {
                    $rule = $rules [1];
                    if (isset ($rules [3])) {
                        $exp = $rules [3];
                    }
                }
                if ($message && strlen($exp) > 0) {
                    $message = __($message, $exp);
                } else if ($message) {
                    $message = __($message);
                }
                $this->validates [$rule] = array('message' => $message, 'option' => $exp);
            }
            $this->required = isset ($this->validates ['required']);
        }
    }

    protected function getProperties($properties = array(), $append = true) {
        if (!is_array($properties)) {
            $properties = array();
        }
        if (isset ($this->option [FWT_OPTIONS]) && !empty ($this->option [FWT_OPTIONS]) && is_array($this->option [FWT_OPTIONS])) {
            $properties = array_merge($properties, $this->option [FWT_OPTIONS]);
        }
        if ($append) {
            $properties ['name'] = $this->name;
            $properties ['id'] = $this->id;
        }
        if (!empty ($this->validates)) {
            $validator = $this->form->useValidator();
            if ($validator) {
                $validator->yield($properties, $this->validates);
            }
        }
        if (!$this->is_valid) {
            if (isset ($properties ['class'])) {
                $properties ['class'] = $properties ['class'] . ' invalid';
            } else {
                $properties ['class'] = 'invalid';
            }
        }
        
        return html_tag_properties($properties);
    }

    public function valid($data, $scope = null) {
        if (!empty($this->validates)) {
            $validator = $this->form->useValidator();
            if ($validator) {
                $valid = $validator->valid($this->value, $data, $this->validates, $scope);
                if ($valid !== true) {
                    $this->is_valid = false;
                    $this->error = $valid;
                    return false;
                }
            }
        }
        $this->error = '';
        $this->is_valid = true;
        return true;
    }

    protected function getBindData() {
        if (isset ($this->option [FWT_BIND]) && is_callable($this->option [FWT_BIND])) {
            return call_user_func_array($this->option [FWT_BIND], array($this->value, $this->form->getInitialData()));
        }
        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getValue($request = null) {
        if (!is_null($request)) {
            $this->value = $request->get($this->name, $this->initial);
        }
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function __get($name) {
        if (isset ($this->option [$name])) {
            return $this->option [$name];
        } else if ($name == 'error') {
            return $this->error;
        } else if ($name == 'valid') {
            return $this->is_valid;
        } else if ($name == 'required') {
            return $this->required;
        }
        return null;
    }
	public function getValidate(){
		$properties = array('validate'=>'');
		if (!empty ($this->validates)) {
            $validator = $this->form->useValidator();
            if ($validator) {
                $validator->yield($properties, $this->validates);
            }
        }
        return $properties['validate'];
	}
    public abstract function getLabelComponent();

    public abstract function getWidgetComponent();

    public abstract function getTipComponent();
}

/**
 *
 */
class TableForm extends BaseForm {
    protected function getFormHead() {
        return "<table>";
    }

    protected function getFormItemWrapper() {
        return '<tr><td>{$label}</td><td>{$widget}</td><td>{$tip}</td></tr>';
    }

    protected function getFormFoot() {
        return "</table>\n";
    }
}

/**
 * 文件控件
 */
class TextWidget extends FormWidget {
    public function getLabelComponent() {
        if ($this->required) {
            return '<label for="' . $this->id . '">' . $this->label . '(<span>*</span>)</label>';
        } else {
            return '<label for="' . $this->id . '">' . $this->label . '</label>';
        }
    }

    public function getWidgetComponent() {
        $properties = $this->getProperties(array('value' => $this->value));
        return '<input type="text" ' . $properties . '/>';
    }

    public function getTipComponent() {
        return $this->error;
    }
}

/**
 * 密码控件
 */
class PasswordWidget extends TextWidget {
    public function getWidgetComponent() {
        $properties = $this->getProperties();
        return '<input type="password" ' . $properties . '/>';
    }
}

/**
 * 下拉列表
 */
class SelectWidget extends TextWidget {
    public function getWidgetComponent() {
        $properties = $this->getProperties();
        $rtn = array('<select' . $properties . '>');
        foreach ($this->getBindData() as $key => $value) {
            if ($this->value == $key) {
                $rtn [] = '<option value="' . $key . '" selected="selected">' . $value . '</option>';
            } else {
                $rtn [] = '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $rtn [] = '</select>';
        return implode("\n", $rtn);
    }
}

/**
 * 多选
 */
class CheckboxWidget extends TextWidget {
    public function getWidgetComponent() {
        $properties = $this->getProperties(false, false);
        $rtn = array();
        foreach ($this->getBindData() as $key => $value) {
            if ($this->value != null && in_array($key, $this->value)) {
                $rtn [] = '<label><input type="checkbox" name="' . $this->name . '[]" value="' . $key . '" checked="checked" ' . $properties . '/>' . $value . '</label>';
            } else {
                $rtn [] = '<label><input type="checkbox" name="' . $this->name . '[]" value="' . $key . '" ' . $properties . '/>' . $value . '</label>';
            }
        }
        return implode("\n", $rtn);
    }
}

/**
 * 可选可不选
 */
class ScheckboxWidget extends TextWidget {
    public function getWidgetComponent() {
        $properties = $this->getProperties();
        if (intval($this->value) == 1) {
            return '<label><input type="checkbox" checked="checked" ' . $properties . '/></label>';
        } else {
            return '<label><input type="checkbox" ' . $properties . '/></label>';
        }
    }

    public function getValue($request = null) {
        if (!is_null($request)) {
            if (isset ($request [$this->name])) {
                $this->value = 1;
            } else {
                $this->value = 0;
            }
        }
        return $this->value;
    }
}

/**
 * 单选
 */
class RadioWidget extends TextWidget {
    public function getWidgetComponent() {
        $properties = $this->getProperties(false, false);
        $rtn = array();
        foreach ($this->getBindData() as $key => $value) {
            if ($this->value == $key) {
                $rtn [] = '<label><input type="radio" name="' . $this->name . '" value="' . $key . '" checked="checked" ' . $properties . '/>' . $value . '</label>';
            } else {
                $rtn [] = '<label><input type="radio" name="' . $this->name . '" value="' . $key . '" ' . $properties . '/>' . $value . '</label>';
            }
        }
        return implode("\n", $rtn);
    }
}

/**
 * 文本域
 */
class TextareaWidget extends TextWidget {
    public function getWidgetComponent() {
        $properties = $this->getProperties();
        return '<textarea ' . $properties . '>' . $this->value . '</textarea>';
    }
}