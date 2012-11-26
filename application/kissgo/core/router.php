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
	public function get_app_action($request) {
		self::$url = $url = $request ['_url'];
		$action = 'index';
		if ($url == '/') {
			$app = '';
		} else {
			$chunks = explode ( '/', trim ( $url, '/' ) );
			$cnt = count ( $chunks );
			if ($cnt == 1) {
				$app = $chunks [0];
			} else if ($cnt == 2) {
				list ( $app, $action ) = $chunks;
			} else {
				$app = array_shift ( $chunks );
				$action = array_shift ( $chunks );
				$request ['_params'] = $chunks;
			}
		}
		return $this->load_application ( $action, $app );
	}
	
	/**
	 * @param string $action
	 * @param string $app
	 * @return string a callable function
	 */
	public function load_application($action, $app = '') {
		$func_name = 'do_default_' . $action;
		if ($app) {
			$app = mpath ( $app );
			if ($app === false) {
				Response::getInstance ()->respond ( 403 );
			}
			$func_name = 'do_' . $action;
			$app = str_replace ( '_', '/', $app ) . "/actions/{$action}.php";
			if (! is_module_file ( $app )) {
				$app = 'index.php';
			}
		} else {
			$app = 'index.php';
		}
		$app_action_file = MODULES_PATH . $app;
		$core_app_action_file = KISSGO . 'modules/' . $app;
		$rtn = null;
		if (is_file ( $app_action_file )) {
			$rtn = include ($app_action_file);
		} else if (is_file ( $core_app_action_file )) {
			$rtn = include ($core_app_action_file);
		} else {
			$func_name = 'do_default_' . $action;
			$app = 'index.php';
			$app_action_file = MODULES_PATH . $app;
			$rtn = include ($app_action_file);
		}
		if (is_callable ( $rtn )) {
			return $rtn;
		} else if (function_exists ( $func_name )) {
			return $func_name;
		} else {
			return $rtn;
		}
	}
}
// END OF FILE router.php