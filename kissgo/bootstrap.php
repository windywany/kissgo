<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
defined('KISSGO') or exit('No direct script access allowed');
// 过滤输入
if (@ini_get('register_globals')) {
    if (isset ($_REQUEST ['GLOBALS'])) {
        die ('GLOBALS overwrite attempt detected');
    }
    $noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', '_KCF');
    $input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset ($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
    foreach ($input as $k => $v) {
        if (!in_array($k, $noUnset) && isset ($GLOBALS [$k])) {
            $GLOBALS [$k] = NULL;
            unset ($GLOBALS [$k]);
        }
    }
}
if (version_compare('5.2', phpversion(), '>')) {
    die (sprintf('Your php version is %s,but kissgo required  php 5.2+', phpversion()));
}
if (!defined('RUNTIME_MEMORY_LIMIT')) {
    define ('RUNTIME_MEMORY_LIMIT', '128M');
}
if (function_exists('memory_get_usage') && (( int )@ini_get('memory_limit') < abs(intval(RUNTIME_MEMORY_LIMIT)))) {
    @ini_set('memory_limit', RUNTIME_MEMORY_LIMIT);
}

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

function __kissgo_error_handler($error_no, $error_str, $error_file, $error_line) {
    if (function_exists('fire')) {
        fire('kissgo_error_raise', $error_no, $error_str, $error_file, $error_line);
    }
    if (function_exists('log_message')) {
        if ($error_no == E_USER_ERROR || $error_no == E_ERROR) {
            log_message($error_str, array('file' => $error_file, 'line' => $error_line), DEBUG_ERROR);
            Response::getInstance()->close(true);
        } else if ($error_no == E_USER_NOTICE) {
            log_message($error_str, array('file' => $error_file, 'line' => $error_line), DEBUG_INFO);
        } else if ($error_no == E_USER_WARNING || $error_no == E_WARNING) {
            log_message($error_str, array('file' => $error_file, 'line' => $error_line), DEBUG_WARN);
        } else if ($error_no != E_NOTICE) {
            log_message($error_str, array('file' => $error_file, 'line' => $error_line), DEBUG_DEBUG);
        }
    } else {
        echo $error_str, ' in file ', $error_file, ' on line ', $error_line;
    }
}

set_error_handler('__kissgo_error_handler');

function __kissgo_exception_handler($exception) {
    if (function_exists('fire')) {
        fire('catch_exception', $exception);
    }
}

set_exception_handler('__kissgo_exception_handler');

if (version_compare(phpversion(), '5.3', '<')) {
    @set_magic_quotes_runtime(0);
}
// no magic quotes
@ini_set('magic_quotes_sybase', 0);

$__ksg_run_level = isset($_SERVER['KSG_RUN_LEVEL']) ? $_SERVER['KSG_RUN_LEVEL'] : '';
// the short for directory separator
define('DS', DIRECTORY_SEPARATOR);
// debug levels
define('DEBUG_INFO', 4);
define('DEBUG_WARN', 3);
define('DEBUG_DEBUG', 2);
define('DEBUG_ERROR', 1);
// load the bootstrap script of the application,in this script you can change some settings
$__ksg_app_bootstrap = APP_PATH . 'bootstrap.php';
if (!empty($__ksg_run_level)) {
    $__ksg_app_bootstrap = APP_PATH . 'bootstrap_' . $__ksg_run_level . '.php';
}
if (is_readable($__ksg_app_bootstrap)) {
    include $__ksg_app_bootstrap;
}
define('APP_DIR', dirname(APP_PATH));
define('KISSGO_DIR', dirname(KISSGO));
// debug level
defined('DEBUG') or define('DEBUG', 3);
// the default application name, this is used by session id
defined('APP_NAME') or define('APP_NAME', basename(WEB_ROOT));
// the default apps path
defined('APPS') or define('APPS', APP_PATH . 'apps' . DS);
// the application data path
defined('APPDATA_PATH') or define('APPDATA_PATH', APP_PATH . 'appdata' . DS);
// the temporary directory path
defined('TMP_PATH') or define('TMP_PATH', APPDATA_PATH . 'tmp' . DS);
// 安全码，用于cookie等内容的加密与解密
defined('SECURITY_KEY') or define ("SECURITY_KEY", 'yeN3g9EbNfi-Zf!dV63dI1j8Fbk5H@L7+6ya}4y7u2j4Mf4|mPg2v?99g4{1k576');
// 是否开启i18n支持
defined('I18N') or define('I18N', false);
defined('GZIP_ENABLED') or define('GZIP_ENABLED', false);
/**
 * 应用程序设置类
 */
class KissGoSetting implements ArrayAccess {
    private $settings = array();
    private static $INSTANCE = array();

    /**
     * @param string $name
     * @return KissGoSetting
     */
    public static function getSetting($name = 'default', $setting = null) {
        if ($setting instanceof KissGoSetting) {
            self::$INSTANCE[$name] = $setting;
        } else if (!isset(self::$INSTANCE[$name]) || !self::$INSTANCE[$name]) {
            self::$INSTANCE[$name] = new KissGoSetting();
        }
        return self::$INSTANCE[$name];
    }

    public static function hasSetting($name) {
        return isset(self::$INSTANCE[$name]);
    }

    public function offsetExists($offset) {
        return isset($this->settings[$offset]);
    }

    public function offsetGet($offset) {
        return $this->settings[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->settings[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->settings[$offset]);
    }

    /**
     * 获取设置
     * @param string $name
     * @param string $default
     * @return string
     */
    public function get($name, $default = '') {
        return isset($this->settings[$name]) ? $this->settings[$name] : $default;
    }

    /**
     * 设置
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value) {
        $this->settings[$name] = $value;
    }

    /**
     * 保存
     */
    public function save() { }
}

/**
 * 存取程序运行中的全局变量
 */
class KissGoValues extends KissGoSetting {

}

// the application settings script
$__ksg_settings_file = APP_PATH . 'conf/settings.php';
if (!empty($__ksg_run_level)) {
    $__ksg_settings_file = APP_PATH . 'conf/settings_' . $__ksg_run_level . '.php';
}
if (is_readable($__ksg_settings_file)) {
    include $__ksg_settings_file;
}

// load kissgo libs scripts
include KISSGO . 'libs/functions.php';
include KISSGO . 'libs/plugin.php';
include KISSGO . 'libs/template.php';
include KISSGO . 'libs/i18n.php';
// load kissgo core scripts
include KISSGO . 'core/request.php';
include KISSGO . 'core/response.php';
include KISSGO . 'core/session.php';
include KISSGO . 'core/cookie.php';
include KISSGO . 'core/cache.php';

include KISSGO . 'core/kissgo.php';
KissGo::getInstance()->run();
// end of file bootstrap.php