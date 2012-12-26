<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author leo
 * @package kissgo
 * @date 12-10-24 21:13
 * $Id$
 */
defined('KISSGO') or exit ('No direct script access allowed');
/**
 * The I18n Class for translating string to local string.
 *
 * @version 1.0
 * @since 1.0
 */
class I18n implements ArrayAccess {
    private static $language = null;
    private $items = array();
    private static $local = 'zh-CN';

    private function __construct() {

    }

    /**
     * set local for this running
     *
     * @param string $local
     */
    public static function setLocal($local = '') {
        if (!defined('I18N_ENABLED') || I18N_ENABLED !== true) {
            return;
        }
        self::$local = 'zh-CN';
        if (empty ($local) && isset ($_SERVER ['HTTP_ACCEPT_LANGUAGE'])) {
            if (preg_match('/([a-z]+\-[A-Z]+)/', $_SERVER ['HTTP_ACCEPT_LANGUAGE'], $m)) {
                self::$local = $m [1];
            }
        } else if (!empty ($local)) {
            self::$local = $local;
        }
        self::append(KISSGO);
        self::append(APP_PATH);
    }

    /**
     * append language definition file
     *
     * @param string $i18n_base_dir
     */
    public static function append($i18n_base_dir) {
        if (!is_dir($i18n_base_dir)) {
            $i18n_base_dir = is_file($i18n_base_dir) ? dirname($i18n_base_dir) . DS : $i18n_base_dir;
        }
        if (is_dir($i18n_base_dir)) {
            $i18n_base_dir .= 'i18n' . DS . self::$local . DS . 'language.php';
            if (is_file($i18n_base_dir)) {
                include $i18n_base_dir;
            }
        }
    }

    /**
     * retrieve a language for setting or translating
     *
     * @return I18n
     */
    public static function getLanguage() {
        if (is_null(self::$language)) {
            self::$language = new I18n ();
        }
        return self::$language;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset ($this->items [$offset]);
    }

    /**
     * @param string $offset
     * @return string
     */
    public function offsetGet($offset) {
        if (isset ($this->items [$offset])) {
            return $this->items [$offset];
        } else {
            return $offset;
        }
    }

    /**
     * @param string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value) {
        $this->items [$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset) {
        unset ($this->items [$offset]);
    }

    public function reset() {
        $this->items = array();
    }
}

/**
 * Translate the string to local string
 *
 * @param string $string the string will be translated
 * @param mixed ... the values for the arguments in $string
 * @return string the translated string
 */
function __($string) {
    static $language = false;
    if (defined('I18N_ENABLED') && I18N_ENABLED) {
        if (!$language) {
            $language = I18n::getLanguage();
        }
        $string = $language [$string];
    }
    $args = func_get_args();
    count($args) > 0 and array_shift($args);
    if (empty ($args)) {
        return $string;
    } else if (is_array($args [0])) {
        return vsprintf($string, $args [0]);
    } else {
        return vsprintf($string, $args);
    }
}

I18n::setLocal();
// end of i18n.php