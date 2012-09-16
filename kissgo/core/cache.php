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
abstract class Cache implements ArrayAccess {
    public $current_group = 'default';
    public $expire = 0;

    public function offsetExists($offset) {
        return $this->has_key($offset, $this->current_group);
    }

    public function offsetGet($offset) {
        return $this->get($offset, $this->current_group);
    }

    public function offsetSet($offset, $value) {
        $this->add($offset, $value, $this->expire, $this->current_group);
    }

    public function offsetUnset($offset) {
        $this->delete($offset, $this->current_group);
    }

    /**
     * 缓存数据
     * @param string $key 缓存唯一键值
     * @param mixed $value 要缓存的数据
     * @param int $expire 缓存时间
     * @param string $group 缓存组
     */
    public abstract function add($key, $value, $expire = 0, $group = 'default');

    /**
     * 从缓存中取数据
     * @param string $key 缓存唯一键值
     * @param string $group 缓存组
     * @return mixed 缓存数据,如果未命中则返回null
     */
    public abstract function get($key, $group = 'default');

    /**
     * 删除一个缓存
     * @param string $key 缓存唯一键值
     * @param string $group 缓存组
     */
    public abstract function delete($key, $group = 'default');

    /**
     * 清空组内所有缓存
     * @param string $group 缓存组
     */
    public abstract function clear($check = true, $group = 'default');

    /**
     * @param string $key
     * @param string $group
     * @return bool
     */
    public abstract function has_key($key, $group = 'default');
}
// END OF FILE cache.php