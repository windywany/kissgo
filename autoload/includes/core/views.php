<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-18 下午9:17
 * $Id$
 */
/**
 * 视图基类
 *
 * 用于定义模板的绘制和头部输出.
 * @abstract
 * @author Leo Ning <leo.ning@like18.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */
abstract class View implements ArrayAccess {
    protected $tpl = '';
    protected $data;
    protected $relatedPath;
    protected $headers = array ();
    
    /**
     * @param string|array $data
     * @param string $tpl
     * @param array $headers
     */
    public function __construct($data = array(), $tpl = '', $headers = array()) {
        if (is_array ( $data )) {
            $this->tpl = str_replace ( '/', DS, $tpl );
            $this->data = $data;
        } else if (is_string ( $data )) {
            $this->tpl = str_replace ( '/', DS, $data );
            $this->data = array ();
        }
        
        if (is_array ( $tpl )) {
            $this->headers = $headers;
        }
    }
    
    public function offsetExists($offset) {
        return isset ( $this->data [$offset] );
    }
    
    public function offsetGet($offset) {
        return $this->data [$offset];
    }
    
    public function offsetSet($offset, $value) {
        $this->data [$offset] = $value;
    }
    
    public function offsetUnset($offset) {
        unset ( $this->data [$offset] );
    }
    
    /**
     * 绘制
     * @internal param array $data
     * @internal param \Response $response
     * @internal param string $tpl 模板
     * @return string
     */
    public abstract function render();
    
    /**
     * set http response header
     */
    public function echoHeader() {
        if (! empty ( $this->headers ) && is_array ( $this->headers )) {
            foreach ( $this->headers as $name => $value ) {
                @header ( "$name: $value", true );
            }
        }
        $this->setHeader ();
    }
    public function setRelatedPath($path) {
        $this->relatedPath = $path;
    }
    /**
     *
     * 设置输出头
     */
    protected function setHeader() {}
}

/**
 * JSON视图
 *
 * 通过json_encode函数输出
 *
 * @author Leo Ning <leo.ning@like18.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */

class JsonView extends View {
    /**
     * @param array|string $data
     * @param array $headers
     */
    public function __construct($data, $headers = array()) {
        parent::__construct ( $data, '', $headers );
    }
    
    /**
     * 绘制
     * @return string
     */
    public function render() {
        return json_encode ( $this->data );
    }
    
    public function setHeader() {
        @header ( 'Content-type: application/json', true );
    }
}

/**
 * HTML视图
 *
 * 使用PHP 语法定义的HTML视图
 *
 * @author Leo Ning <leo.ning@like18.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */
class HtmlView extends View {
    /**
     * 绘制
     * @return string
     */
    public function render() {
        $tpl = '';
        if (is_file ( $tpl )) {
            extract ( $this->data );
            @ob_start ();
            include $tpl;
            $content = @ob_get_contents ();
            @ob_end_clean ();
            return $content;
        } else {
            log_error ( 'The view template ' . $tpl . ' is not found' );
            return '';
        }
    }
    
    public function setHeader() {
        @header ( 'Content-Type: text/html' );
    }
}

/**
 * HTML视图
 *
 * 使用PHP 语法定义的HTML视图
 *
 * @author Leo Ning <leo.ning@like18.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */
class SimpleView extends View {
    /**
     * @param array|string $data
     */
    public function __construct($data) {
        parent::__construct ( array ($data ) );
    }
    
    /**
     * 绘制
     * @return string
     */
    public function render() {
        return array_pop ( $this->data );
    }
    
    public function setHeader() {
        @header ( 'Content-Type: text/html' );
    }
}
class CssView extends View {
    private $etag = '';
    /**
     * @param array|string $data
     */
    public function __construct($data, $etag = '') {
        parent::__construct ( array ($data ) );
        $this->etag = $etag;
    }
    
    /**
     * 绘制
     * @return string
     */
    public function render() {
        return array_pop ( $this->data );
    }
    
    public function setHeader() {
        @header ( 'Content-Type: text/css' );
        if ($this->etag) {
            @header ( 'Etag: ' . $this->etag );
        }
        @header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' );
    }
}
class JsView extends View {
    private $etag = '';
    /**
     * @param array|string $data
     */
    public function __construct($data, $etag = '') {
        parent::__construct ( array ($data ) );
        $this->etag = $etag;
    }
    
    /**
     * 绘制
     * @return string
     */
    public function render() {
        return array_pop ( $this->data );
    }
    
    public function setHeader() {
        @header ( 'Content-Type: text/javascript' );
        if ($this->etag) {
            @header ( 'Etag: ' . $this->etag );
        }
        @header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' );
    }
}
// END OF FILE view.php