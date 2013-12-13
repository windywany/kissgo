<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package includes
 *
 * $Id$
 */
defined ( 'WEB_ROOT' ) or exit ( 'No direct script access allowed' );
// -----------------------------------------------------------------------
//////////////////////////////////////////////////////////////////////////
// debug levels
//////////////////////////////////////////////////////////////////////////
define ( 'DEBUG_OFF', 6 );
define ( 'DEBUG_ERROR', 5 );
define ( 'DEBUG_INFO', 4 );
define ( 'DEBUG_WARN', 3 );
define ( 'DEBUG_DEBUG', 2 );

//////////////////////////////////////////////////////////////////////////
// common constant
//////////////////////////////////////////////////////////////////////////
define ( 'DS', DIRECTORY_SEPARATOR ); // the short for directory separator
define ( 'APP_PATH', WEB_ROOT );
define ( 'INCLUDES', WEB_ROOT . 'includes' . DS );
define ( 'KISSGO', INCLUDES . 'core' . DS );

//////////////////////////////////////////////////////////////////////////
// path constant
//////////////////////////////////////////////////////////////////////////
// the default application name, this is used by session id
defined ( 'APP_NAME' ) or define ( 'APP_NAME', basename ( WEB_ROOT ) );
defined ( 'ADMINCP' ) or define ( 'ADMINCP', 'admincp.php' );
// module directory
defined ( 'MODULE_DIR' ) or define ( 'MODULE_DIR', 'modules' );
define ( 'MODULES_PATH', APP_PATH . MODULE_DIR . DS ); // the default modules path
// application data directory
defined ( 'APPDATA_DIR' ) or define ( 'APPDATA_DIR', 'appdata' );
define ( 'APPDATA_PATH', APP_PATH . APPDATA_DIR . DS ); // the application data path
defined ( 'TMP_PATH' ) or define ( 'TMP_PATH', APPDATA_PATH . 'tmp' . DS ); // the temporary directory path
//theme path
defined ( 'THEME_DIR' ) or define ( 'THEME_DIR', 'themes' );
defined ( 'THEME_PATH' ) or define ( 'THEME_PATH', WEB_ROOT );
defined ( 'MISC_DIR' ) or define ( 'MISC_DIR', 'assets' );
defined ( 'UPLOAD_DIR' ) or define ( 'UPLOAD_DIR', APPDATA_DIR . '/uploads' );
define ( 'UPLOAD_PATH', WEB_ROOT . UPLOAD_DIR . DS );
// 过滤输入
if (@ini_get ( 'register_globals' )) {
    if (isset ( $_REQUEST ['GLOBALS'] )) {
        die ( 'GLOBALS overwrite attempt detected' );
    }
    $noUnset = array ('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES' );
    $input = array_merge ( $_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset ( $_SESSION ) && is_array ( $_SESSION ) ? $_SESSION : array () );
    foreach ( $input as $k => $v ) {
        if (! in_array ( $k, $noUnset ) && isset ( $GLOBALS [$k] )) {
            $GLOBALS [$k] = NULL;
            unset ( $GLOBALS [$k] );
        }
    }
}
if (version_compare ( '5.2', phpversion (), '>' )) {
    die ( sprintf ( 'Your php version is %s,but kissgo required  php 5.2+', phpversion () ) );
}
if (! defined ( 'RUNTIME_MEMORY_LIMIT' )) {
    define ( 'RUNTIME_MEMORY_LIMIT', '256M' );
}
if (function_exists ( 'memory_get_usage' ) && (( int ) @ini_get ( 'memory_limit' ) < abs ( intval ( RUNTIME_MEMORY_LIMIT ) ))) {
    @ini_set ( 'memory_limit', RUNTIME_MEMORY_LIMIT );
}

if (function_exists ( 'mb_internal_encoding' )) {
    mb_internal_encoding ( 'UTF-8' );
}
if (version_compare ( phpversion (), '5.3', '<' )) {
    @set_magic_quotes_runtime ( 0 );
}
@ini_set ( 'magic_quotes_sybase', 0 ); // no magic quotes


/**
 * 应用程序设置类
 */
class KissGoSetting implements ArrayAccess {
    private $setting_name = '';
    private $settings = array ();
    private $pos = 0;
    private static $INSTANCE = array ();

    public function __construct($name) {
        $this->setting_name = $name;
    }

    /**
     * 取系统设置实例
     *
     * @param string $name
     * @param
     * null|KissgoSetting
     * @return KissGoSetting
     */
    public static function getSetting($name = 'default', $setting = null) {
        if ($setting instanceof KissGoSetting) {
            self::$INSTANCE [$name] = $setting;
        } else if (! isset ( self::$INSTANCE [$name] ) || ! self::$INSTANCE [$name]) {
            self::$INSTANCE [$name] = new KissGoSetting ( $name );
        }
        return self::$INSTANCE [$name];
    }

    public static function hasSetting($name) {
        return isset ( self::$INSTANCE [$name] );
    }

    public function offsetExists($offset) {
        return isset ( $this->settings [$offset] );
    }

    public function offsetGet($offset) {
        return $this->settings [$offset];
    }

    public function offsetSet($offset, $value) {
        $this->settings [$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset ( $this->settings [$offset] );
    }

    /**
     * 获取设置
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    public function get($name, $default = '') {
        return isset ( $this->settings [$name] ) ? $this->settings [$name] : $default;
    }

    /**
     * 设置
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value) {
        $this->settings [$name] = $value;
    }

    public function toArray() {
        return $this->settings;
    }
}
global $_kissgo_processing_installation;
$_ksg_settings_file = APPDATA_PATH . 'settings.php'; // the application settings script
if (is_readable ( $_ksg_settings_file )) {
    include_once $_ksg_settings_file;
    $settings = KissGoSetting::getSetting ();
    if (isset ( $settings ['DEBUG'] )) {
        define ( 'DEBUG', intval ( $settings ['DEBUG'] ) );
    }
    if (isset ( $settings ['CLEAN_URL'] )) {
        define ( 'CLEAN_URL', ! empty ( $settings ['CLEAN_URL'] ) );
    } else {
        define ( 'CLEAN_URL', false );
    }
    if (isset ( $settings ['I18N_ENABLED'] )) {
        define ( 'I18N_ENABLED', ! empty ( $settings ['I18N_ENABLED'] ) );
    } else {
        define ( 'I18N_ENABLED', false );
    }
    if (isset ( $settings ['GZIP_ENABLED'] )) {
        define ( 'GZIP_ENABLED', ! empty ( $settings ['GZIP_ENABLED'] ) );
    } else {
        define ( 'GZIP_ENABLED', true );
    }
    if (isset ( $settings ['SECURITY_KEY'] ) && ! empty ( $settings ['SECURITY_KEY'] )) {
        define ( 'SECURITY_KEY', $settings ['SECURITY_KEY'] );
    } else {
        define ( 'SECURITY_KEY', md5 ( __FILE__ ) );
    }
    if (isset ( $settings ['BASE_URL'] ) && ! empty ( $settings ['BASE_URL'] )) {
        define ( 'BASE_URL', rtrim ( $settings ['BASE_URL'], '/' ) . '/' );
    } else {
        $script_name = $_SERVER ['SCRIPT_NAME'];
        $script_name = trim ( str_replace ( WEB_ROOT, '', $script_name ), '/' );
        $script_names = explode ( '/', $script_name );
        array_pop ( $script_names );
        $base = '/';
        if (! empty ( $script_names ) && ! is_file ( WEB_ROOT . $script_name )) {
            $web_roots = explode ( '/', trim ( str_replace ( DS, '/', WEB_ROOT ), '/' ) );
            $matchs = array ();
            $pos = 0;
            foreach ( $web_roots as $chunk ) {
                if ($chunk == $script_names [$pos]) {
                    $matchs [] = $chunk;
                    $pos ++;
                } else {
                    $matchs = array ();
                    $pos = 0;
                }
            }
            if ($pos > 0) {
                $base .= implode ( '/', $matchs ) . '/';
            }
        }
        define ( 'BASE_URL', $base );
    }
    if (isset ( $settings ['TIMEZONE'] ) && ! empty ( $settings ['TIMEZONE'] )) {
        define ( 'TIMEZONE', $settings ['TIMEZONE'] );
    }
    if (isset ( $settings ['DEBUG_FIREPHP'] ) && ! empty ( $settings ['DEBUG_FIREPHP'] )) {
        define ( 'DEBUG_FIREPHP', $settings ['DEBUG_FIREPHP'] );
    }
    if (isset ( $settings ['THEME'] ) && ! empty ( $settings ['THEME'] )) {
        define ( 'THEME', $settings ['THEME'] );
    }
} else if ($_kissgo_processing_installation != true) { // goto install page
//$install_script = detect_app_base_url () . 'install.php';
//echo "<html><head><script type='text/javascript'>var win = window;while (win.location.href != win.parent.location.href) {win = win.parent;} win.location.href = '{$install_script}';</script></head><body></body></html>";
//exit ();
}
unset ( $_ksg_settings_file );
defined ( 'DEBUG' ) or define ( 'DEBUG', DEBUG_DEBUG ); // debug level
defined ( 'TIMEZONE' ) or define ( 'TIMEZONE', 'Asia/Shanghai' );
defined ( 'THEME' ) or define ( 'THEME', 'default' );
define ( 'ASSETS_URL', BASE_URL . MISC_DIR . '/' ); // The url for assets
define ( 'MODULE_URL', BASE_URL . MODULE_DIR . '/' );
define ( 'UPLOAD_URL', BASE_URL . UPLOAD_DIR . '/' );
define ( 'THEME_URL', BASE_URL . THEME_DIR . '/' );
define ( 'ADMINCP_URL', BASE_URL . ADMINCP );
@date_default_timezone_set ( TIMEZONE );
if (isset ( $_GET ['__url'] )) {
    define ( 'REQUEST_URL', $_GET ['__url'] );
}
include KISSGO . 'cache.php'; // load cache instant
if (DEBUG == DEBUG_OFF) {
    error_reporting ( 0 );
    @ini_set ( 'display_errors', 0 );
} else if (DEBUG > DEBUG_DEBUG) {
    error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT );
    @ini_set ( 'display_errors', 1 );
} else {
    error_reporting ( E_ALL & ~ E_NOTICE );
    @ini_set ( 'display_errors', 1 );
}
if (defined ( 'DEBUG_FIREPHP' ) && DEBUG_FIREPHP) {
    include INCLUDES . 'vendors/firephp/fb.php';
}
include KISSGO . 'plugin.php';
include KISSGO . 'functions.php';
include KISSGO . 'template.php';
include KISSGO . 'session.php';
include KISSGO . 'views.php';
include KISSGO . 'phpcrud/phpcrud.php';
include KISSGO . 'rbac.php';

// ////////////////////////////////////////////////////////////
/**
 * 自动类加载器
 *
 * 根据类名和已经注册到系统的类路径{@link $__kissgo_exports}动态加载类
 *
 * @global array 系统类路径
 * @param $clz string
 * 类名
 */
function _kissgo_class_loader($clz) {
    global $__kissgo_exports;
    $key = '_class_' . $clz;
    $clz_file = InnerCacher::get ( $key );
    if ($clz_file) {
        include $clz_file;
        return;
    }
    foreach ( $__kissgo_exports as $path ) {
        $clz_file = $path . DS . $clz . '.php';
        if (is_file ( $clz_file )) {
            InnerCacher::add ( $key, $clz_file );
            include $clz_file;
            return;
        }
    }
    $clz_file = apply_filter ( 'auto_load_class', '', $clz );
    if ($clz_file && file_exists ( $clz_file )) {
        InnerCacher::add ( $key, $clz_file );
        include $clz_file;
    }
}
$__kissgo_exports [] = KISSGO . 'phpcrud';
$__kissgo_exports [] = KISSGO . 'phpcrud' . DS . 'dialects';
$__kissgo_exports [] = INCLUDES . 'classes';
$__kissgo_exports [] = INCLUDES . 'vendors';
$__kissgo_exports [] = INCLUDES . 'vendors' . DS . 'smarty';
spl_autoload_register ( '_kissgo_class_loader' );
$__rqst = Request::getInstance ();
// load modules
$modules = InnerCacher::get ( 'module_list' );
$exports = InnerCacher::get ( 'class_exports' );
if (! $modules) {
    $hd = opendir ( MODULES_PATH );
    if ($hd) {
        $exports = array ();
        while ( ($f = readdir ( $hd )) != false ) {
            if (is_dir ( MODULES_PATH . $f ) && $f != '.' && $f != '..') {
                $plugin = MODULES_PATH . $f . DS . 'plugin.php';
                if (file_exists ( $plugin )) {
                    $modules [] = $plugin;
                }
                $fp = MODULES_PATH . $f . DS . 'forms';
                if (is_dir ( $fp )) {
                    $exports [] = $fp;
                }
                $fp = MODULES_PATH . $f . DS . 'classes';
                if (is_dir ( $fp )) {
                    $exports [] = $fp;
                }
            }
        }
        if (DEBUG > DEBUG_DEBUG) {
            InnerCacher::add ( 'module_list', $modules );
            InnerCacher::add ( 'class_exports', $exports );
        }
    }
}
if ($modules) {
    foreach ( $modules as $m ) {
        include $m;
    }
    if ($exports) {
        foreach ( $exports as $e ) {
            $__kissgo_exports [] = $e;
        }
    }
}
unset ( $modules, $exports );
// modules loaded
fire ( 'engine_initialized' );
// end of file bootstrap.php