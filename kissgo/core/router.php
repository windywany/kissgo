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
     * @param Request $request
     * @return string
     */
    public function get_app_action($request) {

        return $this->load_application('index', 'aaaa');
    }

    /**
     * @param string $action
     * @return string a callable function
     */
    public function load_application($action, $app = '') {
        if ($app) {
            $func_name = 'do_' . $app . '_' . $action;
            $app = str_replace('_', '/', $app) . "/actions/{$action}.php";
        } else {
            $func_name = 'do_default_' . $action;
            $app = 'index.php';
        }
        $app_action_file = APPS . $app;
        $rtn = null;
        if (is_file($app_action_file)) {
            $rtn = include $app_action_file;
        }
        if (function_exists($func_name)) {
            return $func_name;
        } else {
            return $rtn;
        }
    }
}
// END OF FILE router.php