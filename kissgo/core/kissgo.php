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
        // set timezone
        defined('TIMEZONE') or define ('TIMEZONE', apply_filter('get_app_timezone', 'Asia/Shanghai'));
        @date_default_timezone_set(TIMEZONE);
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
        $globals = KissGoValues::getSetting('globals');

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
}
// end of file kissgo.php