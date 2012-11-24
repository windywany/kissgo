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
    $noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
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

// the short for directory separator
define('DS', DIRECTORY_SEPARATOR);
// debug levels
define('DEBUG_INFO', 4);
define('DEBUG_WARN', 3);
define('DEBUG_DEBUG', 2);
define('DEBUG_ERROR', 5);
// load the bootstrap script of the application,in this script you can change some settings
$__ksg_run_level = isset($_SERVER['KSG_RUN_LEVEL']) ? $_SERVER['KSG_RUN_LEVEL'] : '';
$__ksg_app_bootstrap = APP_PATH . 'bootstrap.php';
if (!empty($__ksg_run_level)) {
    $__ksg_app_bootstrap = APP_PATH . 'bootstrap_' . $__ksg_run_level . '.php';
}
if (is_readable($__ksg_app_bootstrap)) {
    include $__ksg_app_bootstrap;
}
// debug level
defined('DEBUG') or define('DEBUG', 3);
// set timezone
defined('TIMEZONE') or define ('TIMEZONE', 'Asia/Shanghai');
@date_default_timezone_set(TIMEZONE);
define('APP_DIR', dirname(APP_PATH));
define('KISSGO_DIR', dirname(KISSGO));
// the default application name, this is used by session id
defined('APP_NAME') or define('APP_NAME', basename(WEB_ROOT));
// the default modules path
defined('MODULES_PATH') or define('MODULES_PATH', APP_PATH . 'modules' . DS);
define('MODULE_DIR', basename(MODULES_PATH));
// the application data path
defined('APPDATA_PATH') or define('APPDATA_PATH', APP_PATH . 'appdata' . DS);
defined('TEMPLATE_PATH') or define('TEMPLATE_PATH', WEB_ROOT . 'templates' . DS);
defined('STATIC_DIR') or define('STATIC_DIR', 'static');
defined('BASE_URL') or define('BASE_URL', '/');
defined('CLEAN_URL') or define('CLEAN_URL', false);
// the temporary directory path
defined('TMP_PATH') or define('TMP_PATH', APPDATA_PATH . 'tmp' . DS);
// 安全码，用于cookie等内容的加密与解密
defined('SECURITY_KEY') or define ("SECURITY_KEY", 'yeN3g9EbNfi-Zf!dV63dI1j8Fbk5H@L7+6ya}4y7u2j4Mf4|mPg2v?99g4{1k576');
// 是否开启i18n支持
defined('I18N_ENABLED') or define('I18N_ENABLED', false);
defined('GZIP_ENABLED') or define('GZIP_ENABLED', false);
define('NOTNULL', '_@_NOT_NULL_@_');
// 常用设置
define('INSTALLED_MODULES', '__INSTALLED_MODULES__');
define('INSTALLED_PLUGINS', '__INSTALLED_PLUGINS__');
define('DATABASE', '__DATABASE__');
define('COOKIE', '__COOKIE__');
define('CACHE', '__CACHE__');
/**
 * 应用程序设置类
 */
class KissGoSetting implements ArrayAccess {
    private $setting_name = '';
    private $settings = array();
    private static $INSTANCE = array();

    public function __construct($name) {
        $this->setting_name = $name;
    }

    /**
     * 取系统设置实例
     *
     * @param string $name
     * @param null|KissgoSetting
     * @return KissGoSetting
     */
    public static function getSetting($name = 'default', $setting = null) {
        if ($setting instanceof KissGoSetting) {
            self::$INSTANCE[$name] = $setting;
        } else if (!isset(self::$INSTANCE[$name]) || !self::$INSTANCE[$name]) {
            self::$INSTANCE[$name] = new KissGoSetting($name);
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
    public function save() {
    }
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
include KISSGO . 'libs/i18n.php';
include KISSGO . 'libs/functions.php';
include KISSGO . 'libs/plugin.php';
include KISSGO . 'libs/template.php';
// load kissgo core scripts
include KISSGO . 'core/path.php';
include KISSGO . 'core/request.php';
include KISSGO . 'core/response.php';
include KISSGO . 'core/rbac.php';
include KISSGO . 'core/router.php';
include KISSGO . 'core/session.php';
include KISSGO . 'core/cache.php';
include KISSGO . 'core/views.php';
include KISSGO . 'core/form.php';
include KISSGO . 'core/table.php';
// load applications and plugins
$__ksg_global_settings = KissGoSetting::getSetting();
$__ksg_installed_plugins = $__ksg_global_settings[INSTALLED_PLUGINS];
if (is_array($__ksg_installed_plugins) && !empty($__ksg_installed_plugins)) {
    $plg_init_files = array();
    foreach ($__ksg_installed_plugins as $plg) {
        if (preg_match('/^::/', $plg)) {
            $plg_init_files[] = str_replace('::', '::plugins/', $plg);
        } else {
            $plg_init_files[] = 'plugins/' . $plg;
        }
    }
    if (!empty($plg_init_files)) {
        includes($plg_init_files);
    }
    unset($plg, $plg_init_files);
}
// ////////////////////////////////////////////////////////////
// 自动加载器
/**
 * 自动类加载器
 *
 * 根据类名和已经注册到系统的类路径{@link $__kissgo_exports}动态加载类
 *
 * @global array 系统类路径
 * @param $clz string
 * 类名
 */
function __kissgo_class_loader($clz) {
    global $__kissgo_exports;
    foreach ($__kissgo_exports as $path) {
        $clz_file = $path . DS . $clz . '.php';
        if (is_file($clz_file)) {
            include $clz_file;
            return;
        }
    }
    $file = apply_filter('auto_load_class', '', $clz);
    if ($file && file_exists($file)) {
        include $file;
    }
}

spl_autoload_register('__kissgo_class_loader');
// load user plugins
if (function_exists('application_plugin_load')) {
    application_plugin_load();
}
// load apps
$__ksg_installed_apps = $__ksg_global_settings[INSTALLED_MODULES];
if (is_array($__ksg_installed_apps)) {
    $app_init_files = array();
    foreach ($__ksg_installed_apps as $app) {
        if (preg_match('/^::/', $app)) {
            $app_init_files[] = str_replace('::', '::modules/', $app) . '/__init__.php';
        } else {
            $app_init_files[] = MODULE_DIR . '/' . $app . '/__init__.php';
        }
    }
    if (!empty($app_init_files)) {
        includes($app_init_files);
    }
    unset($app, $app_init_files);
}
unset($__ksg_installed_apps, $__ksg_installed_plugins);
/////////////////////////////////////////////////////////////////
include KISSGO . 'core/kissgo.php';
// end of file bootstrap.php