<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

/**
 * 核心类，主控类,
 * 可以像使用map一样使用KissGo的实例来取应用的配置
 */
class KissGo {
    private function __construct() {}
    /**
     * 运行，分发请求
     */
    public static function run() {
        $request = Request::getInstance ();
        $sessionAutoStart = ! preg_match ( '#\.(css|js)$#i', $request ['_url'] );
        if ($sessionAutoStart) {
            self::startSession ();
        }
        $response = Response::getInstance ();
        $view = apply_filter ( 'before_route', false );
        if ($view === false) {
            $router = Router::getInstance ();
            list ( $action_func, $args ) = $router->getAction ( $request );
            if (is_callable ( $action_func )) {
                array_unshift ( $args, $request, $response );
                $view = call_user_func_array ( $action_func, $args );
            } else {
                Response::respond ( 404 );
            }
        }
        $response->output ( $view );
    }
    public static function initEngine() {
        static $inited = false;
        if ($inited) {
            return;
        }
        $inited = true;
        if (! @ini_get ( 'zlib.output_compression' ) && @ob_get_status ()) {
            $__ksg_before_out = @ob_get_contents ();
            if ($__ksg_before_out) {
                log_debug ( "BEFORE STARTTING >>> " . $__ksg_before_out );
            }
            @ob_end_clean ();
        }
        @ob_start ( array (Response::getInstance (), 'ob_out_handler' ) );
        if (defined ( 'GZIP_ENABLED' ) && GZIP_ENABLED && extension_loaded ( "zlib" )) {
            $gzip = @ini_get ( 'zlib.output_compression' );
            if (! $gzip) {
                @ini_set ( 'zlib.output_compression', 1 );
            }
        }
    }
    /**
     *
     * 初始化并启动 session，然后你就可以使用$_SESSION来存取SESSION
     */
    public static function startSession() {
        static $startted = false;
        if ($startted) {
            return;
        }
        $startted = true;
        $__ksg_session_handler = apply_filter ( 'get_session_handler', null );
        if ($__ksg_session_handler instanceof SessionHandlerInterface) {
            if (version_compare ( '5.4', phpversion (), '>=' )) {
                session_set_save_handler ( $__ksg_session_handler, true );
            } else {
                session_set_save_handler ( array ($__ksg_session_handler, 'open' ), array ($__ksg_session_handler, 'close' ), array ($__ksg_session_handler, 'read' ), array ($__ksg_session_handler, 'write' ), array ($__ksg_session_handler, 'destroy' ), array ($__ksg_session_handler, 'gc' ) );
                register_shutdown_function ( 'session_write_close' );
            }
        }
        $session_expire = apply_filter ( 'get_session_expire', 900 );
        @session_set_cookie_params ( $session_expire );
        @session_cache_expire ( $session_expire );
        $session_path = apply_filter ( 'get_session_path', '' );
        if (! empty ( $session_path ) && is_dir ( $session_path )) {
            @session_save_path ( $session_path );
        }
        $session_name = apply_filter ( 'get_session_name', md5 ( WEB_ROOT ) );
        $session_id = isset ( $_COOKIE [$session_name] ) ? $_COOKIE [$session_name] : null;
        @session_name ( $session_name );
        if (! empty ( $session_id )) {
            @session_id ( $session_id );
        }
        session_start ();
    }
}
KissGo::initEngine ();
// end of file kissgo.php