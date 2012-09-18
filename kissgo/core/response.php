<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-16 下午5:53
 * $Id$
 */
class Response {
    private $content = '';
    private $view = null;
    private static $INSTANCE = null;

    /**
     * 初始化
     */
    private function __construct() {
        // nothing to do
    }

    /**
     * 得到全局唯一Response实例
     *
     * @return Response
     */
    public static function getInstance() {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new Response();
        }
        return self::$INSTANCE;
    }

    /**
     * set response view instance
     *
     * @param View $view
     */
    public function setView($view) {
        if ($view instanceof View) {
            $this->view = $view;
        }
    }

    /**
     * 禁用浏览器缓存
     */
    public function nocache() {
        $headers = array('Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT', 'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT', 'Cache-Control' => 'no-cache, must-revalidate, max-age=0', 'Pragma' => 'no-cache');
        foreach ($headers as $header => $val) {
            @header($header . ': ' . $val);
        }
    }

    /**
     * 跳转
     *
     * @param string $location 要转到的网址
     * @param string|array $args 参数
     * @param int $status 响应代码
     */
    public function redirect($location, $args = "", $status = 302) {
        global $is_IIS;
        if (!$location) {
            return;
        }
        if (!empty ($args) && is_string($args)) {
            if (strpos($location, '?') !== false) {
                $location .= '&' . $args;
            } else {
                $location .= '?' . $args;
            }
        }
        if ($is_IIS) {
            @header("Refresh: 0;url=$location");
        } else {
            if (php_sapi_name() != 'cgi-fcgi') {
                status_header($status); // This causes problems on IIS and some
            }
            @header("Location: $location", true, $status);
        }
        $this->close(true);
    }

    /**
     * 内部转发
     * @param string $action
     * @return null|View|string|array
     */
    public function forward($action) {
        $request = Request::getInstance();

        $router = Router::getInstance();
        $action_func = $router->load_application($action);
        if (is_callable($action_func)) {
            return call_user_func_array($action_func, array($request, $this));
        }
        return null;
    }

    /**
     * 设置cookie
     *
     * @param string $name 变量名
     * @param null|mixed$value
     * @param null|int $expire
     * @param null|string $path
     * @param null|string $domain
     * @param null|bool $security
     */
    public function set_cookie($name, $value = null, $expire = null, $path = null, $domain = null, $security = null) {
        $settings = KissGoSetting::getSetting();
        $cookie_setting = array_merge2(array('expire' => 0, 'path' => '/', 'domain' => ".", 'security' => false), $settings[COOKIE]);
        if ($expire == null) {
            $expire = intval($cookie_setting['expire']);
        }
        if ($path == null) {
            $path = $cookie_setting['path'];
        }
        if ($domain == null) {
            $domain = $cookie_setting['domain'];
        }
        if ($security == null) {
            $security = $cookie_setting['security'];
        }
        setcookie($name, $value, $expire, $path, $domain, $security);
    }

    /**
     * 输出view产品的内容
     * @param View $view
     */
    public function output($view = null) {
        if ($view instanceof View) {
            $this->view = $view;
        } else if (is_string($view)) {
            $this->view = new SimpleView($view);
        } else if (is_array($view)) {
            $this->view = new JsonView($view);
        }
        if ($this->view instanceof View) {
            $this->view->echoHeader();
            $content = $this->view->render();
            $content = apply_filter('before_output_content', $content);
            echo $content;
        }
    }

    /**
     *
     *
     * 此方法不应该直接调用，用于ob_start处理output buffer中的内容。
     *
     * @param string $content
     * @return string
     */
    public function ob_out_handler($content) {
        $this->content = apply_filter('filter_output_content', $content);
        return $this->content;
    }

    /**
     *
     *
     * 关闭响应，将内容输出的浏览器，同时触发after_content_output勾子
     *
     * @uses fire
     */
    public function close($exit = true) {
        if ($exit) {
            exit ();
        } else {
            fire('after_content_output', $this->content);
        }
    }
}
// END OF FILE response.php