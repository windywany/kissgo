<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-18 下午9:02
 * $Id$
 */
/**
 * Passport
 */
class Passport implements ArrayAccess {
    private static $INSTANCE = array ();
    private $properties = array ();
    private function __construct($uid) {
        $this->properties ['uid'] = $uid;
    }
    
    /**
     * full access for $passport['uid']
     * @return int 用户ID
     */
    public function getUid() {
        return $this->properties ['uid'];
    }
    public function getAccount() {
        return $this->properties ['account'];
    }
    public function getName() {
        return $this->properties ['name'];
    }
    public function setName($name) {
        $this->properties ['name'] = $name;
    }
    public function getEmail() {
        return $this->properties ['email'];
    }
    public function setEmail($email) {
        $this->properties ['email'] = $email;
    }
    public function getLoginIp() {
        return $this->properties ['login_ip'];
    }
    public function getLoginTime() {
        return $this->properties ['login_time'];
    }
    public function getLastLoginIp() {
        return $this->properties ['last_login_ip'];
    }
    public function setLastLoginIp($last_login_ip) {
        $this->properties ['last_login_ip'] = $last_login_ip;
    }
    public function getLastLoginTime() {
        return $this->properties ['last_login_time'];
    }
    public function setLastLoginTime($last_login_time) {
        $this->properties ['last_login_time'] = $last_login_time;
    }
    public function getStatus() {
        return $this->properties ['status'];
    }
    public function setStatus($status) {
        $this->properties ['status'] = $status;
    }
    public function getAvatar() {
        return $this->properties ['avatar'];
    }
    public function setAvatar($avatar) {
        $this->properties ['avatar'] = $avatar;
    }
    public function getType() {
        return $this->properties ['type'];
    }
    public function setType($type) {
        $this->properties ['type'] = $type;
    }
    public function isLogin($login = null) {
        if (is_null ( $login )) {
            return isset ( $this->properties ['login'] ) ? $this->properties ['login'] : false;
        } else {
            $this->properties ['login'] = $login;
            return $login;
        }
    }
    
    /**
     * @param int $uid 用户ID
     * @return Passport 用户护照
     */
    public static function getPassport($uid = 0) {
        $uid = intval ( $uid );
        if (! isset ( self::$INSTANCE [$uid] )) {
            $passport = new Passport ( $uid );
            if ($uid == 0) {
                $loginInfo = LoginInfo::load ();
                if ($loginInfo) {
                    $passport->copy ( $loginInfo );
                }
            }
            self::$INSTANCE [$uid] = apply_filter ( 'get_user_passport', $passport );
        }
        return self::$INSTANCE [$uid];
    }
    public function save() {
        $info = new LoginInfo ( $this->properties ['uid'], $this->properties ['account'], $this->properties ['name'], $this->properties ['login_time'], $this->properties ['login_ip'] );
        $info->login ( $this->properties ['login'] );
        LoginInfo::save ( $info );
    }
    /**
     * @param LoginInfo $info
     */
    public function copy($info) {
        if ($info instanceof LoginInfo) {
            $this->properties ['uid'] = $info->getUid ();
            $this->properties ['account'] = $info->getAccount ();
            $this->properties ['name'] = $info->getName ();
            $this->properties ['login_ip'] = $info->getIp ();
            $this->properties ['login_time'] = $info->getTime ();
            $this->isLogin ( $info->login () );
        }
    }
    public function offsetExists($offset) {
        return isset ( $this->properties [$offset] );
    }
    public function offsetGet($offset) {
        if (isset ( $this->properties [$offset] )) {
            return $this->properties [$offset];
        }
        return null;
    }
    public function offsetSet($offset, $value) {
        $this->properties [$offset] = $value;
    }
    public function offsetUnset($offset) {
        unset ( $this->properties [$offset] );
    }
}

/**
 * 用户登录信息，记录用户登录时间，偿试次数，用户账户，登录IP
 */
class LoginInfo {
    private $count = 0;
    private $account, $time, $ip, $uid, $name;
    private $isLogin = false;
    public function __construct($uid, $account, $name, $time, $ip) {
        $this->uid = $uid;
        $this->account = $account;
        $this->name = $name;
        $this->time = $time;
        $this->ip = $ip;
    }
    public function login($login = null) {
        if ($login !== null) {
            $this->isLogin = $login;
        }
        return $this->isLogin;
    }
    public function getUid() {
        return $this->uid;
    }
    public function getAccount() {
        return $this->account;
    }
    public function getName() {
        return $this->name;
    }
    public function getTime() {
        return $this->time;
    }
    public function getIp() {
        return $this->ip;
    }
    public function blocked() {
        $this->count ++;
        return $this->count <= 5;
    }
    
    /**
     * @return LoginInfo
     */
    public static function load() {
        $info = isset ( $_SESSION ['_USER_LoginInfo_'] ) ? $_SESSION ['_USER_LoginInfo_'] : null;
        if ($info instanceof LoginInfo) {
            return $info;
        }
        return null;
    }
    
    /**
     * @param LoginInfo $loginInfo
     */
    public static function save($loginInfo) {
        if ($loginInfo instanceof LoginInfo) {
            $_SESSION ['_USER_LoginInfo_'] = $loginInfo;
        }
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
    if (! $rbac || ! $rbac instanceof IRbac) {
        $rbac = apply_filter ( 'get_rbac_driver', null );
    }
    if (! $rbac instanceof IRbac) {
        return false;
    }
    $passport = $passport == null ? Passport::getPassport () : $passport;
    return $rbac->icando ( $op, $resource, $passport, IRbac::USER, $reload );
}
/**
 * get the user's passport
 * @param int $uid
 * @return Passport 
 */
function whoami($uid = 0) {
    $passport = Passport::getPassport ( $uid );
    return $passport;
}