<?php
class ProfileManager {
    private static $INSTANCE = null;
    private $profiles = array ();
    private $profiles_opts = array ();
    private function __construct() {
        $this->scanProfile ();
    }
    
    // 扫描安装配置器信息
    private function scanProfile() {
        $profiles = find_files ( INCLUDES . 'profile', '/^profile\.php$/', array (), 1, 1 );
        if (! empty ( $profiles )) {
            foreach ( $profiles as $profile_file ) {
                $id = basename ( dirname ( $profile_file ) );
                include $profile_file;
                $profileClz = ucfirst ( $id ) . 'InstallProfile';
                $this->profiles [$id] = array ('name' => $id, 'profileClz' => $profileClz );
                if (class_exists ( $profileClz ) && is_subclass_of ( $profileClz, 'InstallProfile' )) {
                    $profileCls = new $profileClz ();
                    $this->profiles_opts [$id] = array ('name' => $profileCls->getProfileName (), 'description' => $profileCls->getDescription () );
                }
            }
        }
    }
    /**
     * 
     * get a profile by $id
     * @param string $id
     * @return InstallProfile
     */
    public static function getProfile($id = null) {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new ProfileManager ();
        }
        
        $pm = self::$INSTANCE;
        if ($id != null) {
            if (isset ( $pm->profiles [$id] )) {
                $info = $pm->profiles [$id];
                $name = $info ['name'];
                $clz = $info ['profileClz'];
                include_once INCLUDES . 'profile' . DS . $name . DS . 'profile.php';
                if (class_exists ( $clz )) {
                    return new $clz ();
                }
            }
            return null;
        } else {
            return $pm->profiles_opts;
        }
    }
    /**
     * 
     * get a profile used by install program
     * @param string $id
     * @return InstallProfile
     */
    public static function getInstallProfile() {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new ProfileManager ();
        }
        
        $pm = self::$INSTANCE;
        $profile_ids = array_keys ( $pm->profiles );
        $id = sess_get ( 'INSTALL_PROFILE', $profile_ids [0] );
        if (empty ( $id )) {
            trigger_error ( 'no install profile was specified!', E_USER_ERROR );
        }
        $profile = self::getProfile ( $id );
        if ($profile) {
            return $profile;
        }
        trigger_error ( 'no install profile was specified!', E_USER_ERROR );
    }
}