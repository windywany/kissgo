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

    private function __construct() {
    }

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
        $url = !isset($request['__url']) || empty($request['__url']) ? '/' : $request['__url'];
        $action = 'index';
        if ($url == '/') {
            $app = '';
        } else {
            $chunks = explode('/', trim($url, '/'));
            $cnt = count($chunks);
            if ($cnt == 1) {
                $app = $chunks[0];
            } else if ($cnt == 2) {
                list($app, $action) = $chunks[0];
            } else {
                $app = array_shift($chunks);
                $action = array_shift($chunks);
                $request['__params'] = $chunks;
            }
        }
        return $this->load_application($action, $app);
    }

    /**
     * @param string $action
     * @return string a callable function
     */
    public function load_application($action, $app = '') {
        $func_name = 'do_default_' . $action;
        if ($app) {
            $func_name = 'do_' . $app . '_' . $action;
            $app = str_replace('_', '/', $app) . "/actions/{$action}.php";
        } else {
            $app = 'index.php';
        }
        $app_action_file = APPS . $app;
        $rtn = null;
        if (is_file($app_action_file)) {
            $rtn = include $app_action_file;
        } else {
            $func_name = 'do_default_' . $action;
            $app = 'index.php';
            $app_action_file = APPS . $app;
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