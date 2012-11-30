<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
define ( 'KISSGO_VERISON', '1.0 BETA 1-1130-2012' ); // the version of kissgo
define ( 'DS', DIRECTORY_SEPARATOR ); // the short for directory separator
define ( 'DEBUG_ERROR', 5 ); // debug levels
define ( 'DEBUG_INFO', 4 );
define ( 'DEBUG_WARN', 3 );
define ( 'DEBUG_DEBUG', 2 );
define ( 'APP_DIR', dirname ( APP_PATH ) );
define ( 'KISSGO_DIR', dirname ( KISSGO ) );
defined ( 'APP_NAME' ) or define ( 'APP_NAME', basename ( WEB_ROOT ) ); // the default application name, this is used by session id
defined ( 'MODULES_PATH' ) or define ( 'MODULES_PATH', APP_PATH . 'modules' . DS ); // the default modules path
define ( 'MODULE_DIR', basename ( MODULES_PATH ) );
defined ( 'PLUGIN_PATH' ) or define ( 'PLUGIN_PATH', APP_PATH . 'plugins' . DS );
defined ( 'APPDATA_PATH' ) or define ( 'APPDATA_PATH', APP_PATH . 'appdata' . DS ); // the application data path
defined ( 'TEMPLATE_PATH' ) or define ( 'TEMPLATE_PATH', WEB_ROOT . 'templates' . DS );
defined ( 'STATIC_DIR' ) or define ( 'STATIC_DIR', 'static' );
defined ( 'TMP_PATH' ) or define ( 'TMP_PATH', APPDATA_PATH . 'tmp' . DS ); // the temporary directory path
define ( 'NOTNULL', '_@_NOT_NULL_@_' );
define ( 'INSTALLED_MODULES', '__INSTALLED_MODULES__' );
define ( 'INSTALLED_PLUGINS', '__INSTALLED_PLUGINS__' );
define ( 'DATABASE', '__DATABASE__' );
define ( 'COOKIE', '__COOKIE__' );
define ( 'CACHE', '__CACHE__' );
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
function _kissgo_error_handler($error_no, $error_str, $error_file, $error_line) {
	if (function_exists ( 'fire' )) {
		fire ( 'kissgo_error_raise', $error_no, $error_str, $error_file, $error_line );
	}
	if (function_exists ( 'log_message' )) {
		if ($error_no == E_USER_ERROR || $error_no == E_ERROR) {
			log_message ( $error_str, array ('file' => $error_file, 'line' => $error_line ), DEBUG_ERROR );
			Response::getInstance ()->close ( true );
		} else if ($error_no == E_USER_NOTICE) {
			log_message ( $error_str, array ('file' => $error_file, 'line' => $error_line ), DEBUG_INFO );
		} else if ($error_no == E_USER_WARNING || $error_no == E_WARNING) {
			log_message ( $error_str, array ('file' => $error_file, 'line' => $error_line ), DEBUG_WARN );
		} else if ($error_no != E_NOTICE) {
			log_message ( $error_str, array ('file' => $error_file, 'line' => $error_line ), DEBUG_DEBUG );
		}
	} else {
		echo $error_str, ' in file ', $error_file, ' on line ', $error_line;
	}
}
set_error_handler ( '_kissgo_error_handler' );
function _kissgo_exception_handler($exception) {
	if (function_exists ( 'fire' )) {
		fire ( 'catch_exception', $exception );
	}
}
set_exception_handler ( '_kissgo_exception_handler' );

if (version_compare ( phpversion (), '5.3', '<' )) {
	@set_magic_quotes_runtime ( 0 );
}
@ini_set ( 'magic_quotes_sybase', 0 ); // no magic quotes
function detect_app_base_url() {
	$script_name = $_SERVER ['SCRIPT_NAME'];
	$script_name = trim ( str_replace ( WEB_ROOT, '', $script_name ), '/' );
	$script_names = explode ( '/', $script_name );
	array_pop ( $script_names );
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
			return '/' . implode ( '/', $matchs ) . '/';
		}
	}
	return '/';
}
/**
 * 应用程序设置类
 */
class KissGoSetting implements ArrayAccess {
	private $setting_name = '';
	private $settings = array ();
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
	public function prepareSettings() {
		$this->settings [INSTALLED_MODULES] = array ('::kissgo', '::passport' );
		if (isset ( $this->settings ['DEBUG'] )) {
			define ( 'DEBUG', intval ( $this->settings ['DEBUG'] ) );
		}
		if (isset ( $this->settings ['CLEAN_URL'] )) {
			define ( 'CLEAN_URL', ! empty ( $this->settings ['CLEAN_URL'] ) );
		} else {
			define ( 'CLEAN_URL', false );
		}
		if (isset ( $this->settings ['I18N_ENABLED'] )) {
			define ( 'I18N_ENABLED', ! empty ( $this->settings ['I18N_ENABLED'] ) );
		} else {
			define ( 'I18N_ENABLED', false );
		}
		if (isset ( $this->settings ['GZIP_ENABLED'] )) {
			define ( 'GZIP_ENABLED', ! empty ( $this->settings ['GZIP_ENABLED'] ) );
		} else {
			define ( 'GZIP_ENABLED', true );
		}
		if (isset ( $this->settings ['SECURITY_KEY'] ) && ! empty ( $this->settings ['SECURITY_KEY'] )) {
			define ( 'SECURITY_KEY', $this->settings ['SECURITY_KEY'] );
		} else {
			define ( 'SECURITY_KEY', md5 ( __FILE__ ) );
		}
		if (isset ( $this->settings ['BASE_URL'] ) && ! empty ( $this->settings ['BASE_URL'] )) {
			define ( 'BASE_URL', rtrim ( $this->settings ['BASE_URL'] ) . '/' );
		}
		if (isset ( $this->settings ['TIMEZONE'] ) && ! empty ( $this->settings ['TIMEZONE'] )) {
			define ( 'TIMEZONE', $this->settings ['TIMEZONE'] );
		}
	}
	/**
	 * 保存
	 */
	public function save() {
		if (function_exists ( 'apply_filter' )) {
			return apply_filter ( 'save_settings_' . $this->setting_name, $this->settings );
		}
	}
}

/**
 * 存取程序运行中的全局变量
 */
class KissGoValues extends KissGoSetting {
}
global $_kissgo_processing_installation;
$_ksg_settings_file = APPDATA_PATH . 'settings.php'; // the application settings script
if (is_readable ( $_ksg_settings_file )) {
	include $_ksg_settings_file;
	include APP_PATH . 'version.php';
	$settings = KissGoSetting::getSetting ();
	$settings->prepareSettings ();
	unset ( $settings );
} else if ($_kissgo_processing_installation != true) { // goto install page
	$install_script = detect_app_base_url () . 'install.php';
	echo "<html><head><script type='text/javascript'>var win = window;while (win.location.href != win.parent.location.href) {win = win.parent;} win.location.href = '{$install_script}';</script></head><body></body></html>";
	exit ();
}
unset ( $_ksg_settings_file );
defined ( 'DEBUG' ) or define ( 'DEBUG', 3 ); // debug level
defined ( 'TIMEZONE' ) or define ( 'TIMEZONE', 'Asia/Shanghai' );
defined ( 'BASE_URL' ) or define ( 'BASE_URL', detect_app_base_url () );
@date_default_timezone_set ( TIMEZONE );
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
	foreach ( $__kissgo_exports as $path ) {
		$clz_file = $path . DS . $clz . '.php';
		if (is_file ( $clz_file )) {
			include $clz_file;
			return;
		}
	}
	$file = apply_filter ( 'auto_load_class', '', $clz );
	if ($file && file_exists ( $file )) {
		include $file;
	}
}
spl_autoload_register ( '_kissgo_class_loader' );
///////////////////////////////////////////////////////////////
function _kissgo_load_modules($modules) {
	global $_ksg_installed_modules;
	if (is_array ( $modules )) {
		$app_init_files = array ();
		foreach ( $modules as $app ) {
			$app_path = $app;
			if (preg_match ( '/^::/', $app )) {
				$app_path = ltrim ( $app, ':' );
				$app_init_files [] = str_replace ( '::', '::modules/', $app ) . '/__init__.php';
			} else {
				$app_init_files [] = MODULE_DIR . '/' . $app . '/__init__.php';
			}
			$_ksg_installed_modules [] = $app_path;
		}
		if (! empty ( $app_init_files )) {
			includes ( $app_init_files );
		}
	}
}
function _kissgo_load_plugins($plugins) {
	if (is_array ( $plugins ) && ! empty ( $plugins )) {
		$plg_init_files = array ();
		foreach ( $plugins as $plg ) {
			if (preg_match ( '/^::/', $plg )) {
				$plg_init_files [] = str_replace ( '::', '::plugins/', $plg );
			} else {
				$plg_init_files [] = 'plugins/' . $plg;
			}
		}
		if (! empty ( $plg_init_files )) {
			includes ( $plg_init_files );
		}
	}
}
// load applications and plugins
$_ksg_global_settings = KissGoSetting::getSetting ();
global $_ksg_installed_plugins;
$_ksg_installed_plugins = $_ksg_global_settings [INSTALLED_PLUGINS];
_kissgo_load_plugins ( $_ksg_installed_plugins );
global $_ksg_installed_apps, $_ksg_installed_modules;
$_ksg_installed_apps = $_ksg_global_settings [INSTALLED_MODULES];
_kissgo_load_modules ( $_ksg_installed_apps );
//////////////////////////////////////////////////////////////////
fire ( 'kissgo_startted' );
// end of file bootstrap.php