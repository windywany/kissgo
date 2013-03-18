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
    private static $extensionManager;
    private function __construct() {
        self::$extensionManager = ExtensionManager::getInstance ();
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
     * 
     * url是否可用，检测url是否与当前的模块路径有冲突
     * @param string $url
     * @return boolean
     */
    public static function urlIsAvailable($url) {
        return true;
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
     * @param string $controller
     * @param string $alias
     * @return string a callable function name
     */
    public function load_application($action, $controller = '', $alias = '') {
        $actions [3] = array ('index.php', 'do_show_custom_page', false );
        $alias = strtolower ( $alias );
        $controller = strtolower ( $controller );
        $action = strtolower ( $action );
        $module = $alias;
        if ($alias) {
            $module = self::$extensionManager->getModuleByAlias ( $alias );
            if (! empty ( $controller )) {
                $actions [1] = array ("{$module}/actions/{$controller}/{$action}.php", "do_{$module}_{$controller}", true );
                $actions [2] = array ("{$module}/actions/{$controller}.php", "do_{$module}_{$controller}", true );
            } else {
                $actions [1] = array ("{$module}/actions/{$action}.php", "do_{$module}", true );
                $actions [2] = array ("{$module}/actions/{$action}/index.php", "do_{$module}", true );
            }
            if (! is_module_file ( $module )) {
                unset ( $actions );
                $actions [] = array ('index.php', 'do_show_custom_page', false );
            }
        }
        ksort ( $actions );
        $suffix = Request::isPost () ? '_post' : '_get';
        if ($action == 'index') {
            $cb_suffixes = array ('_index' . $suffix, '_index', $suffix, '' );
        } else {
            $cb_suffixes = array ('_' . $action . $suffix, '_' . $action );
        }
        foreach ( $actions as $act ) {
            list ( $af, $func_name, $ismoule ) = $act;
            $app_action_file = MODULES_PATH . $af;
            if (is_file ( $app_action_file )) {
                //如果模块有别名，则模块只能通过别名访问
                if ($ismoule && $module == $alias && self::$extensionManager->hasAlias ( $module )) {
                    continue;
                }
                include_once $app_action_file;
                foreach ( $cb_suffixes as $suffix ) {
                    if (function_exists ( $func_name . $suffix )) {
                        return $func_name . $suffix;
                    }
                }
            }
        }
        Response::respond ( 404 );
    }
}
// END OF FILE router.php