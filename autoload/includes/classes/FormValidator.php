<?php

/*
 * basic validator kissgo framework that keep it simple and stupid, go go go ~~
 * @author Leo Ning @package kissgo $Id$
 */
class FormValidator {
    protected static $extra_methods = array ();
    private $messageFile;
    private $formName;
    private $messages;

    public function __construct($messageFile, $formName) {
        $this->messageFile = $messageFile;
        $this->formName = $formName;
    }

    public function getRuleClass($rules, $data = array(), $field = '') {
        $rs = array ();
        $ms = array ();
        foreach ( $rules as $m => $exp ) {
            switch ($m) {
                case 'required' :
                    if ($exp ['option']) {
                        $rs [$m] = "#$exp ['option']";
                    } else {
                        $rs [$m] = true;
                    }
                    break;
                case 'callback' :
                    if ($exp ['option'] [0] == '@') {
                        $m = 'remote';
                        $exps = substr ( $exp ['option'], 1 );
                        $exps = explode ( ',', $exps );
                        $url = ADMINCP_URL . '/ajax/validate/' . $this->formName . '/' . $exps [0] . '/' . $field . '/';
                        if (count ( $exps ) > 1) {
                            array_shift ( $exps );
                            $args = array ();
                            foreach ( $exps as $f ) {
                                if (isset ( $data [$f] )) {
                                    $args [$f] = $data [$f];
                                }
                            }
                            if ($args) {
                                $url .= '?' . http_build_query ( $args );
                            }
                        }
                        $exp ['option'] = $url;
                    }
                case 'min' :
                case 'max' :
                case 'minlength' :
                case 'maxlength' :
                case 'notEqual' :
                case 'gt' :
                case 'ge' :
                case 'lt' :
                case 'le' :
                case 'regexp' :
                case 'remote' :
                case 'accept' :
                    $rs [$m] = $exp ['option'];
                    break;
                case 'gt2' :
                case 'ge2' :
                case 'lt2' :
                case 'le2' :
                case 'equalTo' :
                case 'notEqualTo' :
                    $rs [$m] = "#$exp ['option']";
                    break;
                case 'range' :
                case 'rangelength' :
                    $lens = explode ( ",", $exp ['option'] );
                    $rs [$m] = array (intval ( $lens [0] ), intval ( $lens [1] ) );
                    break;
                case 'num' :
                    $m = 'number';
                case 'email' :
                case 'url' :
                case 'url3' :
                case 'number' :
                case 'digits' :
                case 'date' :
                case 'ip' :
                default :
                    $rs [$m] = true;
                    break;
            }
            $ms [$m] = __ ( $exp ['message'] );
        }
        return array ($rs, $ms );
    }

    public static function add_method($rule, $callable) {
        if (is_callable ( $callable )) {
            self::$extra_methods [$rule] = $callable;
        }
    }

    public function valid($value, $data, $rules, $scope) {
        foreach ( $rules as $rule => $option ) {
            $valid = true;
            $valid_m = 'v_' . $rule;
            if (method_exists ( $this, $valid_m )) {
                $valid = $this->$valid_m ( $value, $option ['option'], $data, $scope, $option ['message'] );
            } else if (isset ( self::$extra_methods [$rule] )) {
                $valid_m = self::$extra_methods [$rule];
                if (is_callable ( $valid_m )) {
                    $valid = call_user_func_array ( $valid_m, array ($value, $option ['option'], $data, $scope, $option ['message'] ) );
                }
            }
            if ($valid !== true) {
                return $valid;
            }
        }
        return true;
    }

    // 必填项目
    protected function v_required($value, $exp, $data, $scope, $message) {
        if ($exp) {
            $exp = explode ( ':', $exp );
            $exp = array_shift ( $exp );
            if (! isset ( $data [$exp] ) || $this->emp ( $data [$exp] )) {
                return true;
            }
        }
        if (! $this->emp ( $value )) {
            return true;
        } else {
            return empty ( $message ) ? __ ( 'This field is required.' ) : $message;
        }
    }

    // 相等
    protected function v_equalTo($value, $exp, $data, $scope, $message) {
        $rst = false;
        if (isset ( $data [$exp] )) {
            $rst = $value == $data [$exp];
        }
        if ($rst) {
            return true;
        } else {
            return empty ( $message ) ? __ ( 'Please enter the same value again.' ) : __ ( $message );
        }
    }

    // 不相等
    protected function v_notEqualTo($value, $exp, $data, $scope, $message) {
        $rst = false;
        if (isset ( $data [$exp] )) {
            $rst = $value != $data [$exp];
        }
        if ($rst) {
            return true;
        } else {
            return empty ( $message ) ? __ ( 'Please enter the different value.' ) : __ ( $message );
        }
    }

    // 不相等
    protected function v_notEqual($value, $exp, $data, $scope, $message) {
        $rst = $value != $exp;
        if ($rst) {
            return true;
        } else {
            return empty ( $message ) ? __ ( 'Please enter the different value.' ) : __ ( $message );
        }
    }

    // 数值,包括整数与实数
    protected function v_num($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value ) || is_numeric ( $value )) {
            return true;
        } else {
            return empty ( $message ) ? __ ( 'Please enter a valid number.' ) : __ ( $message );
        }
    }

    protected function v_number($value, $exp, $data, $scope, $message) {
        return $this->v_num ( $value, $exp, $data, $scope, $message );
    }

    // 整数
    protected function v_digits($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value ) || preg_match ( '/^\d+$/', $value )) {
            return true;
        } else {
            return empty ( $message ) ? __ ( 'Please enter only digits.' ) : __ ( $message );
        }
    }

    // min
    protected function v_min($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $value = floatval ( $value );
        if ($value >= floatval ( $exp )) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than or equal to %s.', $exp ) ) : __ ( $message, $exp );
        }
    }

    // max
    protected function v_max($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $value = floatval ( $value );
        if ($value <= floatval ( $exp )) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than or equal to %s.' ), $exp ) : __ ( $message, $exp );
        }
    }

    // gt 大于
    protected function v_gt($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value > $exp) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than %s.' ), $exp ) : __ ( $message, $exp );
        }
    }

    // gt 大于 表单中的值
    protected function v_gt2($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value > $data [$exp]) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
        }
    }

    // ge 大于等于
    protected function v_ge($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value >= $exp) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than or equal to %s.' ), $exp ) : __ ( $message, $exp );
        }
    }

    // ge2 大于等于
    protected function v_ge2($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value >= $data [$exp]) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value greater than or equal to %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
        }
    }

    // gt 小于
    protected function v_lt($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value < $exp) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than %s.' ), $exp ) : __ ( $message, $exp );
        }
    }

    // gt 小于
    protected function v_lt2($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value < $data [$exp]) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
        }
    }

    // ge 小于等于
    protected function v_le($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value <= $exp) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than or equal to %s.' ), $exp ) : __ ( $message, $exp );
        }
    }

    // ge2 小于等于
    protected function v_le2($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if ($value <= $data [$exp]) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter a value less than or equal to %s.' ), $data [$exp] ) : __ ( $message, $data [$exp] );
        }
    }

    // 取值范围
    protected function v_range($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $exp = explode ( ',', $exp );
        if (count ( $exp ) >= 2) {
            $value = floatval ( $value );
            if ($value >= $exp [0] && $value <= $exp [1]) {
                return true;
            } else {
                return empty ( $message ) ? sprintf ( __ ( 'Please enter a value between %s and %s.' ), $exp [0], $exp [1] ) : __ ( $message, $exp [0], $exp [1] );
            }
        }
        return true;
    }

    // minlength
    protected function v_minlength($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $value = function_exists ( 'mb_strlen' ) ? mb_strlen ( $value ) : strlen ( $value );
        if ($value >= intval ( $exp )) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter at least %s characters.' ), $exp ) : __ ( $message, $exp );
        }
    }

    // maxlength
    protected function v_maxlength($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $value = function_exists ( 'mb_strlen' ) ? mb_strlen ( $value ) : strlen ( $value );
        if ($value <= intval ( $exp )) {
            return true;
        } else {
            return empty ( $message ) ? sprintf ( __ ( 'Please enter no more than %s characters.' ), $exp ) : __ ( $message, $exp );
        }
    }

    // rangelength
    protected function v_rangelength($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $exp = explode ( ',', $exp );
        if (is_array ( $exp ) && count ( $exp ) >= 2) {
            $value = function_exists ( 'mb_strlen' ) ? mb_strlen ( $value ) : strlen ( $value );
            if ($value >= intval ( $exp [0] ) && $value <= intval ( $exp [1] )) {
                return true;
            } else {
                return empty ( $message ) ? sprintf ( __ ( 'Please enter a value between %s and %s characters long.' ), $exp [0], $exp [1] ) : __ ( $message, $exp [0], $exp [1] );
            }
        }
        return true;
    }

    // 用户自定义校验函数
    protected function v_callback($value, $exp, $data, $scope, $message) {
        if ($exp [0] == '@') {
            $exp = substr ( $exp, 1 );
            $exps = explode ( ',', $exp );
            $func = array ($scope, array_shift ( $exps ) );
        }else{
             $exps = explode ( ',', $exp );
            $func =array_shift ( $exps );
        }
        if (is_callable ( $func )) {
            return call_user_func_array ( $func, array ($value, $data, __ ( $message ) ) );
        }
        return empty ( $message ) ? __ ( 'error callback' ) : __ ( $message );
    }

    // 正则表达式
    protected function v_regexp($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }

        if (@preg_match ( $exp, $value )) {
            return true;
        } else {
            return empty ( $message ) ? __ ( 'Please enter a value with a valid extension.' ) : __ ( $message );
        }
    }

    // email
    protected function v_email($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if (function_exists ( 'filter_var' )) {
            $rst = filter_var ( $value, FILTER_VALIDATE_EMAIL );
        } else {
            $rst = preg_match ( '/^[_a-z0-9\-]+(\.[_a-z0-9\-]+)*@[a-z0-9][a-z0-9\-]+(\.[a-z0-9-]*)*$/i', $value );
        }
        return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid email address.' ) : __ ( $message ));
    }

    // url
    protected function v_url($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if (function_exists ( 'filter_var' )) {
            $rst = filter_var ( $value, FILTER_VALIDATE_URL );
        } else {
            $rst = preg_match ( '/^[a-z]+://[^\s]$/i', $value );
        }
        return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid URL.' ) : __ ( $message ));
    }

    protected function v_url3($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $rst = preg_match ( '/^((http|ftp)s?:\/\/|\/).*$/', $value );
        return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid URL.' ) : __ ( $message ));
    }

    // url
    protected function v_ip($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        if (function_exists ( 'filter_var' )) {
            $rst = filter_var ( $value, FILTER_VALIDATE_IP, $exp == '6' ? FILTER_FLAG_IPV6 : FILTER_FLAG_IPV4 );
        } else {
            $rst = ip2long ( $value ) === false ? false : true;
        }
        return $rst ? true : (empty ( $message ) ? __ ( 'Please enter a valid IP.' ) : __ ( $message ));
    }

    // date:true
    // date:"-"
    // date:"msg"
    // date:["-","msg"]
    protected function v_date($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $sp = is_string ( $exp ) && strlen ( $exp ) == 1 ? $exp : '-';
        $value = explode ( $sp, $value );
        if (count ( $value ) == 3 && is_int ( $value [2] ) && @checkdate ( $value [1], $value [2], $value [0] )) {
            return true;
        }
        return empty ( $message ) ? __ ( 'Please enter a valid date.' ) : __ ( $message );
    }

    // datetime:true
    // datetime:"-"
    // datetime:"msg"
    // datetime:["-","msg"]
    protected function v_datetime($value, $exp, $data, $scope, $message) {
        if ($this->emp ( $value )) {
            return true;
        }
        $sp = is_string ( $exp ) && strlen ( $exp ) == 1 ? $exp : '-';
        $times = explode ( ' ', $value );
        $value = explode ( $sp, $times [0] );
        if (count ( $value ) == 3 && isset ( $times [1] ) && @checkdate ( $value [1], $value [2], $value [0] )) {
            $time = explode ( ':', $times [1] );
            if (count ( $time ) == 3 && $time [0] >= 0 && $time [0] < 24 && $time [1] >= 0 && $time [1] < 59 && $time [2] >= 0 && $time [2] < 59) {
                return true;
            }
        }
        return empty ( $message ) ? __ ( 'Please enter a valid datetime.' ) : __ ( $message );
    }

    protected function emp($value) {
        return strlen ( trim ( $value ) ) == 0;
    }

    private function loadMessage() {
        $locale = I18n::getLocale ();
        $file = $this->messageFile . $locale . '.php';
        if (file_exists ( $file )) {
            include_once file;
        }
    }
}