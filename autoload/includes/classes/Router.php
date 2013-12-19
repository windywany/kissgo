<?php

/**
 *
 * framework router
 * @author guangfeng.ning
 *
 */
class Router {
    private static $INSTANCE;

    private function __construct() {

    }

    /**
     * get the system router
     *
     * @return Router
     */
    public static function getRouter() {
        if (! self::$INSTANCE) {
            self::$INSTANCE = new Router ();
        }
        return self::$INSTANCE;
    }

    /**
     * dispatch request
     *
     * @param string $do
     */
    public function route($do) {
        global $__rqst;
        $controllers = explode ( '/', $do );
        $pms = array ();
        $len = count ( $controllers );
        if ($len == 1) {
            $module = $controllers [0];
            $controller = false;
            $action = 'index';
        } else if ($len == 2) {
            $module = $controllers [0];
            $controller = ucfirst ( $controllers [1] );
            $action = 'index';
        } else if ($len == 3) {
            $module = $controllers [0];
            $controller = ucfirst ( $controllers [1] );
            $action = $controllers [2];
        } else if ($len > 3) {
            $module = $controllers [0];
            $controller = ucfirst ( $controllers [1] );
            $action = $controllers [2];
            $pms = array_slice ( $controllers, 3 );
        } else {
            die ( 'Can not dispatch the request:' . $do );
        }
        if (! $controller) {
            $controller_file = MODULES_PATH . $module . DS . 'controller.php';
            $controllerClz = ucfirst ( $module ) . 'Controller';
        } else {
            $controllerClz = ucfirst ( $module ) . $controller . 'Controller';
            $controller_file = MODULES_PATH . $module . DS . 'controllers' . DS . $controllerClz . '.php';
            if (! file_exists ( $controller_file )) {
                if ($pms != 'index') {
                    array_unshift ( $pms, $action );
                }
                $action = $controller;
                $controller_file = MODULES_PATH . $module . DS . 'controller.php';
                $controllerClz = ucfirst ( $module ) . 'Controller';
            }
        }
        if (file_exists ( $controller_file )) {
            include $controller_file;
        }

        if (class_exists ( $controllerClz ) && is_subclass_of2 ( $controllerClz, 'Controller' )) {
            try {
                $rm = $_SERVER ['REQUEST_METHOD'];
                $clz = new $controllerClz ( $module . '/views/', $__rqst, Response::getInstance (), $action, $rm );
                if (method_exists ( $clz, $action . '_' . $rm )) {
                    $action = $action . '_' . $rm;
                }
                $ref = new ReflectionObject ( $clz );
                $method = $ref->getMethod ( $action );
                if ($method) {
                    $params = $method->getParameters ();
                    $args = array ();
                    if ($params) {
                        $idx = 0;
                        foreach ( $params as $p ) {
                            $name = $p->getName ();
                            $def = isset ( $pms [$idx] ) ? $pms [$idx] : ($p->isDefaultValueAvailable () ? $p->getDefaultValue () : null);
                            $value = rqst ( $name, $def, true );
                            $args [] = $value;
                            $idx ++;
                        }
                    }
                    call_user_func_array ( array ($clz, 'preRun' ), array () );
                    $view = call_user_func_array ( array ($clz, $action ), $args );
                    $view = call_user_func_array ( array ($clz, 'postRun' ), array ($view ) );
                    $res = Response::getInstance ();
                    $res->output ( $view );
                } else {
                    die ( 'the mothed of ' . $controllerClz . ' is not found.' );
                }
            } catch ( ReflectionException $e ) {
                if (DEBUG == DEBUG_DEBUG) {
                    die ( $e->getMessage () );
                } else {
                    Response::respond ( 404 );
                }
            }
        } else {
            Response::respond ( 404 );
        }
    }

    public function cmsDispatch() {
        echo 'cms';
    }
}