<?php
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
        if (1 == count ( $controllers )) {
            $controller = $controllers [0];
            $action = 'index';
        } else if (2 <= count ( $controllers )) {
            $controller = $controllers [0];
            $action = $controllers [1];
            $pms = array_slice ( $controllers, 2 );
        } else {
            die ( 'Can not dispatch the request:' . $do );
        }
        $controller_file = MODULES_PATH . $controller . DS . 'controller.php';
        if (file_exists ( $controller_file )) {
            include $controller_file;
        }
        $controllerClz = ucfirst ( $controller ) . 'Controller';
        if (class_exists ( $controllerClz ) && is_subclass_of2 ( $controllerClz, 'Controller' )) {
            try {
                $rm = $_SERVER ['REQUEST_METHOD'];
                $clz = new $controllerClz ( $controller . '/views/', $__rqst, Response::getInstance (), $action, $rm );
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
                    die ( 'the mothed of ' . $controller . ' is not found.' );
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
}