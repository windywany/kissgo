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

/**
 * 核心类，主控类,
 * 可以像使用map一样使用KissGo的实例来取应用的配置
 */
class KissGo implements ArrayAccess {
    private static $INSTANCE = NULL;
    private static $SESSION_STARTED = false;

    private function __construct() {
        if (!@ini_get('zlib.output_compression') && @ob_get_status()) {
            @ob_end_clean();
        }
        @ob_start(array(Response::getInstance(), 'ob_out_handler'));
        if (defined('GZIP_ENABLED') && GZIP_ENABLED && extension_loaded("zlib")) {
            $gzip = @ini_get('zlib.output_compression');
            if (!$gzip) {
                @ini_set('zlib.output_compression', 1);
            }
        }
        $this->initialize_session();
    }

    /**
     * 得到全局唯一的KissGo实例
     *
     * @return KissGo
     */
    public static function getInstance() {
        if (self::$INSTANCE == NULL) {
            self::$INSTANCE = new KissGo();
        }
        return self::$INSTANCE;
    }

    /**
     * 运行，分发请求
     */
    public function run() {
        $request = Request::getInstance();
        echo $_COOKIE['leo'];
    }

    public function offsetExists($offset) {
        return KissGoSetting::hasSetting($offset);
    }

    public function offsetGet($offset) {
        return KissGoSetting::getSetting($offset);
    }

    public function offsetSet($offset, $value) {
        KissGoSetting::getSetting($offset, $value);
    }

    public function offsetUnset($offset) {
        // nothing to do
    }

    /**
     *
     * 初始化 session，然后你就可以使用$_SESSION来存取SESSION
     */
    private function initialize_session() {
        $__ksg_session_handler = apply_filter('get_session_handler', null);
        if ($__ksg_session_handler instanceof SessionHandlerInterface) {
            session_set_save_handler(
                array($__ksg_session_handler, 'open'),
                array($__ksg_session_handler, 'close'),
                array($__ksg_session_handler, 'read'),
                array($__ksg_session_handler, 'write'),
                array($__ksg_session_handler, 'destroy'),
                array($__ksg_session_handler, 'gc')
            );
            register_shutdown_function('session_write_close');
        }
        $session_expire = apply_filter('get_session_expire', 900);
        @session_set_cookie_params($session_expire);
        @session_cache_expire($session_expire);
        $session_path = apply_filter('get_session_path', '');
        if (!empty ($session_path) && is_dir($session_path)) {
            @session_save_path($session_path);
        }
        $session_name = apply_filter('get_session_name', strtoupper(APP_NAME) . '_SID');
        $session_id = isset ($_COOKIE [$session_name]) ? $_COOKIE [$session_name] : null;
        @session_name($session_name);
        if (!empty($session_id)) {
            @session_id($session_id);
        }
        session_start();
    }
}
// end of file kissgo.php