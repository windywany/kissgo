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
    private static $cacheKey = '';
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
        global $_ksg_router_url;
        self::$url = $url = trim ( $request ['_url'], '/' );
        self::$cacheKey = md5 ( $_SERVER ['REQUEST_METHOD'] . ' ' . $url );
        $actionInfo = InnerCacher::get ( self::$cacheKey );
        // if we have a cache for this request.
        if ($actionInfo) {
            if (isset ( $actionInfo ['ac'] ) && $actionInfo ['ac']) {
                list ( $action, $controller, $alias ) = $this->parseURL ( $url );
                $module = self::$extensionManager->getModuleByAlias ( $alias );
                if ($module == null || ($module == $alias && self::$extensionManager->hasAlias ( $module ))) {
                    Response::respond ( 404 );
                }
            }
            include_once $actionInfo ['file'];
            
            if ($actionInfo ['c']) {
                $_ksg_router_url = $actionInfo ['c'];
                $e = '#^' . $_ksg_router_url . '/?#i';
                $args = preg_replace ( $e, '', $url );
                $args = strlen ( $args ) > 0 ? explode ( '/', $args ) : array ();
                $_ksg_router_url = clean_url ( $_ksg_router_url );
                return array ($actionInfo ['func'], $args );
            } else {
                $_ksg_router_url = Request::getVirtualPageUrl ();
                return array ($actionInfo ['func'], array () );
            }
        }
        //all url with suffix '.html or htm', we think it is a static page
        if (preg_match ( '#\.(htm[l]?|css|js)$#i', $url )) {
            $app_action_file = MODULES_PATH . 'index.php';
            $cb_func = 'do_show_custom_page';
            include_once $app_action_file;
            InnerCacher::add ( self::$cacheKey, array ('file' => $app_action_file, 'func' => $cb_func, 'ac' => false, 'c' => '' ) );
            $_ksg_router_url = Request::getVirtualPageUrl ();
            return array ($cb_func, array () );
        } else if ($url == 'install.test.clean.url') {
            status_header ( 200 );
            Response::getInstance ()->close ();
        }
        list ( $action, $controller, $module ) = $this->parseURL ( $url );
        return $this->load_application ( $action, $controller, $module, $url );
    }
    /**
     * Enter description here ...
     * @param url     
     */
    private function parseURL($url) {
        $action = 'index';
        $controller = '';
        if ($url == '/' || empty ( $url )) {
            $module = '';
        } else {
            $chunks = explode ( '/', $url );
            $cnt = count ( $chunks );
            if ($cnt == 1) {
                $module = $chunks [0];
            } else if ($cnt == 2) {
                list ( $module, $action ) = $chunks;
            } else if ($cnt >= 3) {
                $module = array_shift ( $chunks );
                $action = array_pop ( $chunks );
                $controller = implode ( '/', $chunks );
            }
        }
        return array ($action, $controller, $module );
    }
    /**
     * 加载url请求对应的实现文件并查找实现方法
     * @param string $action
     * @param string $controller
     * @param string $alias
     * @return string a callable function name
     */
    public function load_application($action, $controller = '', $alias = '', $url = '') {
        global $_ksg_router_url;
        $alias = strtolower ( $alias );
        $controller = strtolower ( $controller );
        $action = strtolower ( $action );
        $suffix = Request::isPost () ? '_post' : '_get';
        $actions [9999] = array ('index.php', 'do_show_custom_page', false, '', array ('_index' . $suffix, '_index', $suffix, '' ) );
        
        $module = $alias;
        if ($alias) {
            $module = self::$extensionManager->getModuleByAlias ( $alias );
            if ($module == null) {
                log_warn ( 'module: ' . $alias . ' dose not exist!' );
                Response::respond ( 404 );
            }
            if ($action == 'index') {
                $cbks = array ('_index' . $suffix, '_index', $suffix, '' );
            } else {
                $cbks = array ('_' . $action . $suffix, '_' . $action );
            }
            if (! empty ( $controller )) {
                $ctrl_func = str_replace ( '/', '_', $controller );
                $actions [1] = array ("{$module}/actions/{$controller}/{$action}.php", "do_{$module}_{$ctrl_func}", true, $controller . '/' . $action, $cbks );
                $actions [2] = array ("{$module}/actions/{$controller}/index.php", "do_{$module}_{$ctrl_func}", true, $controller, array ($suffix, '' ) );
                $actions [3] = array ("{$module}/actions/{$controller}.php", "do_{$module}_{$ctrl_func}", true, $controller, array ($suffix, '' ) );
                $slices = explode ( '_', $ctrl_func );
                $idx = 3;
                if (count ( $slices ) > 1) {
                    do {
                        $action = array_pop ( $slices );
                        $controller = implode ( '/', $slices );
                        $cbks = array ('_' . $action . $suffix, '_' . $action );
                        $ctrl_func = str_replace ( '/', '_', $controller );
                        $actions [$idx ++] = array ("{$module}/actions/{$controller}/{$action}.php", "do_{$module}_{$ctrl_func}", true, $controller . '/' . $action, $cbks );
                        $actions [$idx ++] = array ("{$module}/actions/{$controller}/index.php", "do_{$module}_{$ctrl_func}", true, $controller, array ($suffix, '' ) );
                        $actions [$idx ++] = array ("{$module}/actions/{$controller}.php", "do_{$module}_{$ctrl_func}", true, $controller, array ($suffix, '' ) );
                    } while ( count ( $slices ) > 1 );
                }                
            } else {
                $actions [1] = array ("{$module}/actions/{$action}.php", "do_{$module}", true, $action, $cbks );
                $actions [2] = array ("{$module}/actions/{$action}/index.php", "do_{$module}", true, $action, $cbks );
            }
            if (! is_module_file ( $module )) {
                unset ( $actions );
                $actions [] = array ('index.php', 'do_show_custom_page', false, '', array ('_index' . $suffix, '_index', $suffix, '' ) );
            }
        }
        ksort ( $actions );
        
        foreach ( $actions as $act ) {
            list ( $af, $func_name, $ismoule, $cc, $cb_suffixes ) = $act;
            $app_action_file = MODULES_PATH . $af;
            if (is_file ( $app_action_file )) {
                //如果模块有别名，则模块只能通过别名访问
                if ($ismoule && $module == $alias && self::$extensionManager->hasAlias ( $module )) {
                    continue;
                }
                include_once $app_action_file;
                foreach ( $cb_suffixes as $suffix ) {
                    $cb_func = $func_name . $suffix;
                    if (function_exists ( $cb_func )) {
                        $_ksg_router_url = $alias . (empty ( $cc ) ? '' : '/' . $cc);
                        $e = '#^' . $_ksg_router_url . '/?#i';
                        InnerCacher::add ( self::$cacheKey, array ('file' => $app_action_file, 'func' => $cb_func, 'ac' => $ismoule ? $module : false, 'c' => $_ksg_router_url ) );
                        $args = preg_replace ( $e, '', $url );
                        $args = strlen ( $args ) > 0 ? explode ( '/', $args ) : array ();
                        $_ksg_router_url = clean_url ( $_ksg_router_url );
                        return array ($cb_func, $args );
                    }
                }
            }
        }
        Response::respond ( 404 );
    }
}
// END OF FILE router.php