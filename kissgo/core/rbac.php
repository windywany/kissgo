<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-18 下午9:02
 * $Id$
 */
/**
 *
 */
class Passport implements ArrayAccess {
    private static $INSTANCE = array();
    private $properties = array();

    private function __construct($uid) {
        $this->properties['uid'] = $uid;
    }

    public function getUid() {
        return $this->properties['uid'];
    }

    public function getName() {
        return $this->properties['name'];
    }

    public function getEmail() {
        return $this->properties['email'];
    }

    public function getLoginIp() {
        return $this->properties['login_ip'];
    }

    public function setLoginIp($login_ip) {
        $this->properties['login_ip'] = $login_ip;
    }

    public function getLoginTime() {
        return $this->properties['login_time'];
    }

    public function setLoginTime($login_time) {
        $this->properties['login_time'] = $login_time;
    }

    public function getLastLoginIp() {
        return $this->properties['last_login_ip'];
    }

    public function setLastLoginIp($last_login_ip) {
        $this->properties['last_login_ip'] = $last_login_ip;
    }

    public function getLastLoginTime() {
        return $this->properties['last_login_time'];
    }

    public function setLastLoginTime($last_login_time) {
        $this->properties['last_login_time'] = $last_login_time;
    }

    public function getStatus() {
        return $this->properties['status'];
    }

    public function setStatus($status) {
        $this->properties['status'] = $status;
    }

    public function getAvatar() {
        return $this->properties['avatar'];
    }

    public function setAvatar($avatar) {
        $this->properties['avatar'] = $avatar;
    }

    public function isLogin($login = null) {
        if (is_null($login)) {
            return $this->properties['login'];
        } else {
            $this->properties['login'] = $login;
            return $login;
        }
    }

    /**
     * @param int $uid 用户ID
     * @return Passport 用户护照
     */
    public static function getPassport($uid = 0) {
        $uid = intval($uid);
        if (!isset(self::$INSTANCE[$uid])) {
            self::$INSTANCE[$uid] = apply_filter('get_user_passport', new Passport($uid));
        }
        return self::$INSTANCE[$uid];
    }

    public function offsetExists($offset) {
        return isset($this->properties[$offset]);
    }


    public function offsetGet($offset) {
        if (isset($this->properties[$offset])) {
            return $this->properties[$offset];
        }
        return null;
    }

    public function offsetSet($offset, $value) {
        $this->properties[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->properties[$offset]);
    }
}

/**
 * RABC 权限检验接口
 * @author windywany
 */
interface IRbac {
    const USER = 'USER'; //用户

    /**
     * Can I do some operation on resource? If I can return true, else return false
     *
     * @param string $op 操作
     * @param string $resource 资源
     * @param Passport $passport 访问者护照
     * @param string $type 访问者类型
     * @param boolean $inherit 继承
     * @return mixed 无权操作时返回false,反之返回extra信息
     */
    function icando($op, $resource, $passport, $type, $reload = false);
}

/**
 * if the user can perform the op on $resource
 * @param $op
 * @param $resource
 * @param Passport $passport
 * @param bool $reload if reload the permissions of the user
 * @return mixed  false when the user is not allowed to perform the op on $resource, or true or array contains extra data
 */
function icando($op, $resource, $passport = null, $reload = false) {
    static $rbac = false;
    if (!$rbac || !$rbac instanceof IRbac) {
        $rbac = apply_filter('get_rbac_driver', null);
    }
    if (!$rbac instanceof IRbac) {
        return false;
    }
    $passport = $passport == null ? Passport::getPassport() : $passport;
    return $rbac->icando($op, $resource, $passport, IRbac::USER, $reload);
}

/**
 * 是否有权访问
 * @param $op
 * @param $resource
 */
function access_checking($op, $resource, $redirect = '', $timeout = 5) {
    if (!icando($op, $resource)) {
        show_error_message(__('Access denied'), __('You do not have permission to perform %s operator on the resource %s!', $op, $resource), $redirect, $timeout);
    }
}