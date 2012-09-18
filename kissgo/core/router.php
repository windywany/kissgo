<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-18 下午9:02
 * $Id$
 */
class Router {
    private static $INSTANCE = null;

    private function __construct() { }

    /**
     * 取得路由器实例
     *
     * @return Router
     */
    public static function getInstance() {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new Router();
        }
        return self::$INSTANCE;
    }

    /**
     * 根据请求，得到相应的app action
     * @param $request
     * @return string
     */
    public function get_app_action($request) {
        return $this->load_application('aaaa.admin_index');
    }

    /**
     * @param string $action
     * @return string a callable function
     */
    public function load_application($action) {
        $chunks = explode('.', $action);
        $func_name = array_pop($chunks);
        $func_name .= '_action';
        if (!function_exists($func_name)) {
            if ($chunks) {
                $app = implode('/', $chunks) . '/actions.php';
            } else {
                $app = 'index.php';
            }
            $app_action_file = APPS . $app;
            if (is_file($app_action_file)) {
                include_once $app_action_file;
            }
        }
        return $func_name;
    }
}
// END OF FILE router.php