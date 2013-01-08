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
    private static $url = '';
    private static $forward_url = '';
    private function __construct() {
    }
    
    /**
     * 取得路由器实例
     *
     * @return Router
     */
    public static function getInstance() {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new Router ();
        }
        return self::$INSTANCE;
    }
    
    /**
     * 根据请求，得到相应的app action
     * @param Request $request
     * @return string
     */
    public function getAction($request) {
        self::$url = $url = $request ['_url'];
        $action = 'index';
        $controller = '';
        if ($url == '/') {
            $module = '';
        } else {
            $chunks = explode ( '/', trim ( $url, '/' ) );
            $cnt = count ( $chunks );
            if ($cnt == 1) {
                $module = $chunks [0];
            } else if ($cnt == 2) {
                list ( $module, $action ) = $chunks;
            } else if ($cnt == 3) {
                list ( $module, $controller, $action ) = $chunks;
            } else if ($cnt > 0) {
                $module = array_shift ( $chunks );
                $controller = array_shift ( $chunks );
                $action = array_shift ( $chunks );
                $request ['_params'] = $chunks;
            }
        }
        return $this->load_application ( $action, $controller, $module );
    }
    
    /**
     * @param string $action
     * @param string $app
     * @return string a callable function
     */
    public function load_application($action, $controller = '', $module = '') {
        $actions [3] = array ('index.php', 'do_default_' . $action );
        $module = strtolower ( $module );
        $controller = strtolower ( $controller );
        $action = strtolower ( $action );
        if ($module) {
            $module = mpath ( $module );
            if ($module === false) {
                Response::respond ( 403 );
            }
            if (! empty ( $controller )) {
                $actions [1] = array ("{$module}/actions/{$controller}/{$action}.php", "do_{$module}_{$controller}_{$action}" );
                $actions [2] = array ("{$module}/actions/{$controller}.php", "do_{$module}_{$controller}_{$action}" );
            } else {
                $actions [1] = array ("{$module}/actions/{$action}.php", "do_{$module}_{$action}" );
            }
            if (! is_module_file ( $module )) {
                unset ( $actions );
                $actions [] = array ('index.php', 'do_default_' . $action );
            }
        }
        ksort ( $actions );
        $suffix = Request::isPost () ? '_post' : '_get';
        foreach ( $actions as $act ) {
            list ( $af, $func_name ) = $act;
            $app_action_file = MODULES_PATH . $af;
            if (is_file ( $app_action_file )) {
                include $app_action_file;
                if (function_exists ( $func_name . $suffix )) {
                    return $func_name . $suffix;
                } else if (function_exists ( $func_name )) {
                    return $func_name;
                }
            }
        }
        Response::respond ( 404 );
    }
}
// END OF FILE router.php