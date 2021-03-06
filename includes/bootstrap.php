<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
defined ( 'WEB_ROOT' ) or exit ( 'No direct script access allowed' );
/////////////////////////////////////////////////////////////////////
// Version configuration
/////////////////////////////////////////////////////////////////////
define ( 'KISSGO_VERSION', "1.0 BETA" );
define ( 'KISSGO_BUILD', "1024" );
// common constants 
define ( 'DS', DIRECTORY_SEPARATOR ); // the short for directory separator
define ( 'APP_PATH', WEB_ROOT );
define ( 'INCLUDES', WEB_ROOT . 'includes' . DS );
define ( 'KISSGO', INCLUDES . 'kissgo' . DS );

define ( 'DEBUG_ERROR', 5 ); // debug levels
define ( 'DEBUG_INFO', 4 );
define ( 'DEBUG_WARN', 3 );
define ( 'DEBUG_DEBUG', 2 );
define ( 'DEBUG_OFF', 6 );

defined ( 'APP_NAME' ) or define ( 'APP_NAME', basename ( WEB_ROOT ) ); // the default application name, this is used by session id
defined ( 'MODULES_PATH' ) or define ( 'MODULES_PATH', APP_PATH . 'modules' . DS ); // the default modules path
define ( 'MODULE_DIR', basename ( MODULES_PATH ) );
defined ( 'APPDATA_PATH' ) or define ( 'APPDATA_PATH', APP_PATH . 'appdata' . DS ); // the application data path
defined ( 'THEME_PATH' ) or define ( 'THEME_PATH', WEB_ROOT );
defined ( 'THEME_DIR' ) or define ( 'THEME_DIR', 'themes' );
defined ( 'STATIC_DIR' ) or define ( 'STATIC_DIR', 'static' );
defined ( 'TMP_PATH' ) or define ( 'TMP_PATH', APPDATA_PATH . 'tmp' . DS ); // the temporary directory path
define ( 'NOTNULL', '_@_NOT_NULL_@_' );
define ( 'DATABASE', '__DATABASE__' );
define ( 'COOKIE', '__COOKIE__' );
define ( 'DATE_FORMAT', '_DATE_FORMAT_' );
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
    define ( 'RUNTIME_MEMORY_LIMIT', '128M' );
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
function detect_app_base_url($full = false) {
    $script_name = $_SERVER ['SCRIPT_NAME'];
    $script_name = trim ( str_replace ( WEB_ROOT, '', $script_name ), '/' );
    $script_names = explode ( '/', $script_name );
    array_pop ( $script_names );
    $base = '/';
    if ($full) {
        $port = $_SERVER ['SERVER_PORT'];
        $https = empty ( $_SERVER ['HTTPS'] ) ? false : true;
        if (! empty ( $https ) && $https != 'off') {
            $http = 'https://';
        } else {
            $http = 'http://';
        }
        if (($https && $port != 443) || (! $https && $port != 80)) {
            $port = ':' . $port;
        } else {
            $port = '';
        }
        $base = $http . $_SERVER ['HTTP_HOST'] . $port . $base;
    }
    
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
    return $base;
}
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
    ///////////////////////////////////////
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
    
    ///////////////////////////////////////    
} else if ($_kissgo_processing_installation != true) { // goto install page
    $install_script = detect_app_base_url () . 'install.php';
    echo "<html><head><script type='text/javascript'>var win = window;while (win.location.href != win.parent.location.href) {win = win.parent;} win.location.href = '{$install_script}';</script></head><body></body></html>";
    exit ();
}
unset ( $_ksg_settings_file );
defined ( 'DEBUG' ) or define ( 'DEBUG', DEBUG_ERROR ); // debug level
defined ( 'TIMEZONE' ) or define ( 'TIMEZONE', 'Asia/Shanghai' );
defined ( 'BASE_URL' ) or define ( 'BASE_URL', detect_app_base_url () );
defined ( 'THEME' ) or define ( 'THEME', 'defaults' );
@date_default_timezone_set ( TIMEZONE );
if (isset ( $_GET ['__url'] )) {
    define ( 'REQUEST_URL', $_GET ['__url'] );
}
include KISSGO . 'core/cache.php'; // load cache instant
// log function
function log_message($message, $trace_info, $level, $origin = null) {
    static $fb = false;
    static $log_name = array (DEBUG_INFO => 'INFO', DEBUG_WARN => 'WARN', DEBUG_DEBUG => 'DEBUG', DEBUG_ERROR => 'ERROR' );
    if ($level < DEBUG_OFF) {
        if ($level >= DEBUG_DEBUG) {
            $msg = date ( "Y-m-d H:i:s" ) . "{$trace_info[0]['file']} - [{$trace_info[0]['line']}] - {$message}\n";
            @error_log ( $msg, 3, APPDATA_PATH . '/logs/kissgo.log' );
        }
        if (defined ( 'DEBUG_FIREPHP' ) && DEBUG_FIREPHP) {
            if (! $fb) {
                $fb = true;
                FB::setEnabled ( true );
            }
            $msg = $origin ? $origin : "{$trace_info[0]['file']} - [{$trace_info[0]['line']}] - {$message}";
            switch ($level) {
                case DEBUG_ERROR :
                    FB::error ( $msg );
                    break;
                case DEBUG_INFO :
                    FB::info ( $msg );
                    break;
                case DEBUG_WARN :
                    FB::warn ( $msg );
                    break;
            }
        } else if (DEBUG == DEBUG_DEBUG && ! Request::isAjaxRequest ()) {
            trigger_error ( $message, E_USER_WARNING );
        }
    }
}
if (DEBUG > DEBUG_DEBUG) {
    error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT );
    @ini_set ( 'display_errors', 0 );
    function _kissgo_error_handler($error_no, $error_str, $error_file, $error_line) {
        $trace_info = array (0 => array ('file' => $error_file, 'line' => $error_line, 'message' => $error_str, 'error no' => $error_no ) );
        if ($error_no == E_USER_ERROR || $error_no == E_ERROR) {
            log_message ( $error_str, $trace_info, DEBUG_ERROR, $trace_info );
            Response::getInstance ()->close ( true );
        } else if ($error_no == E_USER_NOTICE) {
            log_message ( $error_str, $trace_info, DEBUG_INFO, $trace_info );
        } else if ($error_no == E_USER_WARNING || $error_no == E_WARNING) {
            log_message ( $error_str, $trace_info, DEBUG_WARN, $trace_info );
        }
    }
    set_error_handler ( '_kissgo_error_handler' );
    function _kissgo_exception_handler($exception) {
        log_message ( html_entity_decode ( $exception->getMessage () ), array (array ('file' => $exception->getFile (), 'line' => $exception->getLine () ) ), DEBUG_ERROR, $exception );
        Response::getInstance ()->close ( true );
    }
    set_exception_handler ( '_kissgo_exception_handler' );
} else {
    error_reporting ( E_ALL & ~ E_NOTICE );
    @ini_set ( 'display_errors', 1 );
}
// load kissgo libs scripts
if (defined ( 'DEBUG_FIREPHP' ) && DEBUG_FIREPHP) {
    include INCLUDES . 'vendors/firephp/fb.php';
}
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
include KISSGO . 'core/views.php';
include KISSGO . 'core/form.php';
include KISSGO . 'core/grid.php';
include KISSGO . 'core/kissgo.php';
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
spl_autoload_register ( '_kissgo_class_loader' );
///////////////////////////////////////////////////////////////
// load applications and plugins
global $_ksg_installed_modules;
ExtensionManager::getInstance ()->loadInstalledExtensions ();
$__rqst = Request::getInstance ();
//////////////////////////////////////////////////////////////////
fire ( 'kissgo_startted' );
// end of file bootstrap.php