<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-16 下午6:16
 * $Id$
 */
/**
 * cache基类
 */
class Cache implements ArrayAccess {
    public $current_group = 'default';
    public $expire = 0;
    /**
     * 取系统缓存管理器
     * @return Cache
     */
    public static function getCache() {
        static $cache = false;
        if ($cache === false) {
            $cache = apply_filter ( 'get_cache_manager', null );
            if (! $cache instanceof Cache) {
                $cache = new Cache ();
            }
        }
        return $cache;
    }
    public function offsetExists($offset) {
        return $this->has_key ( $offset, $this->current_group );
    }
    public function offsetGet($offset) {
        return $this->get ( $offset, $this->current_group );
    }
    public function offsetSet($offset, $value) {
        $this->add ( $offset, $value, $this->expire, $this->current_group );
    }
    public function offsetUnset($offset) {
        $this->delete ( $offset, $this->current_group );
    }
    
    /**
     * 缓存数据
     * @param string $key 缓存唯一键值
     * @param mixed $value 要缓存的数据
     * @param int $expire 缓存时间
     * @param string $group 缓存组
     */
    public function add($key, $value, $expire = 0, $group = 'default') {}
    
    /**
     * 从缓存中取数据
     * @param string $key 缓存唯一键值
     * @param string $group 缓存组
     * @return mixed 缓存数据,如果未命中则返回null
     */
    public function get($key, $group = 'default') {
        return null;
    }
    
    /**
     * 删除一个缓存
     * @param string $key 缓存唯一键值
     * @param string $group 缓存组
     */
    public function delete($key, $group = 'default') {}
    
    /**
     * 清空组内所有缓存
     * @param string $group 缓存组
     */
    public function clear($check = true, $group = 'default') {}
    
    /**
     * @param string $key
     * @param string $group
     * @return bool
     */
    public function has_key($key, $group = 'default') {
        return false;
    }
}
/**
 * 
 * inner php cache
 * @author guangfeng.ning
 *
 */
class InnerCache {
    public function add($key, $data) {
        return true;
    }
    public function remove($key) {
        return true;
    }
    public function get($key) {
        return null;
    }
    public function clear() {

    }
}
/**
 * 
 * APC Cache
 * @author guangfeng.ning
 *
 */
class ApcCacher extends InnerCache {
    public function add($key, $data) {
        apc_store ( $key, $data );
        return true;
    }
    public function remove($key) {
        apc_delete ( $key );
        return true;
    }
    public function get($key) {
        return apc_fetch ( $key );
    }
    public function clear() {
        return apc_clear_cache ( 'user' );
    }
}
/**
 * 
 * XCache
 * @author guangfeng.ning
 *
 */
class XCacheCacher extends InnerCache {
    public function add($key, $data) {
        xcache_set ( $key, $data );
        return true;
    }
    public function remove($key) {
        @xcache_unset ( $key );
        return true;
    }
    public function get($key) {
        if (xcache_isset ( $key )) {
            return xcache_get ( $key );
        }
        return null;
    }
    public function clear() {
        return xcache_clear_cache ( XC_TYPE_VAR );
    }
}
/**
 * 
 * Inner Cache Wrapper
 * @author guangfeng.ning
 *
 */
class InnerCacher {
    private static $CACHE;
    private static $PRE;
    public static function init() {
        if (self::$CACHE == null) {
            self::$PRE = md5 ( WEB_ROOT );
            if (function_exists ( 'apc_store' )) {
                self::$CACHE = new ApcCacher ();
            } else if (function_exists ( 'xcache_get' )) {
                self::$CACHE = new XCacheCacher ();
            } else {
                self::$CACHE = new InnerCache ();
            }
        }
    }
    public static function add($key, $data) {
        $key = self::$PRE.$key;
        return self::$CACHE->add ( $key, $data );
    }
    public static function get($key) {
        $key = self::$PRE.$key;
        return self::$CACHE->get ( $key );
    }
    public static function remove($key) {
        $key = self::$PRE.$key;
        return self::$CACHE->remove ( $key );
    }
    public static function clear() {
        self::$CACHE->clear ();
    }
}
InnerCacher::init ();
// you can do somthing to improve your application performance in APPDATA_PATH/cache.php file.
// but in this file, you just can use little functionality Kissgo provided.
// you might write this script carefully so that it will not slow down your APP.
if (file_exists ( APPDATA_PATH . 'cache.php' )) {
    include APPDATA_PATH . 'cache.php';
}
// END OF FILE cache.php