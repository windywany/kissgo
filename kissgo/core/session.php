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
 * Session基类
 *
 */
abstract class Session implements ArrayAccess {
    public final function offsetExists($offset) {
        return $this->has($offset);
    }

    public final function offsetGet($offset) {
        return $this->get($offset);
    }

    public final function offsetSet($offset, $value) {
        $this->add($offset, $value);
    }

    public final function offsetUnset($offset) {
        $this->add($offset, null);
    }

    /**
     * 设置SESSION,当$value=null时从SESSION中删除$name对应的值
     * @param string $name 值名
     * @param mixed $value 值
     */
    public abstract function add($name, $value = null);

    /**
     * 从SESSION中取值
     * @param string $name 值名
     * @param mixed $default 默认值
     * @return mixed
     */
    public abstract function get($name, $default = "");

    /**
     * session中是否有$name对应的值
     * @param string $name
     * @return bool
     */
    public abstract function has($name);

    /**
     * 关闭会话
     */
    public abstract function close();

    /**
     * 销毁会话
     */
    public abstract function destroy();

    /**
     *
     * 当前会话的ID
     */
    public abstract function id();
}
// END OF FILE session.php