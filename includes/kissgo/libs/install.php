<?php
/*
 * Install library
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
class KissGOInstaller {
    public $error = '';
    public function __construct() {
        if (isset ( $_SESSION ['_INSTALL_DB_DATA'] )) {
            $dbForm = new InstallDbForm ( $_SESSION ['_INSTALL_DB_DATA'] );
            $db = $dbForm->validate ( null, true );
        }
        if (isset ( $_SESSION ['_INSTALL_CONFIG_DATA'] )) {
            $configForm = new InstallConfigForm ( $_SESSION ['_INSTALL_CONFIG_DATA'] );
            $config = $configForm->validate ( null, true );
        }
        $setting = KissGoSetting::getSetting ();
        $default_setting_file = APPDATA_PATH . 'default.settings.php';
        if (is_file ( $default_setting_file )) {
            include_once $default_setting_file;
        }
        if ($config) {
            $settings ['DEBUG'] = $config ['debug'];
            $settings ['CLEAN_URL'] = $config ['clean_url'] ? true : false;
            $settings ['I18N_ENABLED'] = $config ['i18n'] ? true : false;
            $settings ['GZIP_ENABLED'] = $config ['gzip'] ? true : false;
            $settings ['TIMEZONE'] = $config ['timezone'];
            $settings ['SECURITY_KEY'] = $config ['security_key'];
            $settings ['date_format'] = $config ['date_format'];
            $settings ['site_name'] = $config ['site_name'];
        }
        if ($db) {
            $db_default = array ('driver' => $db ['driver'], 'encoding' => 'UTF8', 'host' => $db ['host'], 'port' => $db ['port'], 'prefix' => $db ['prefix'], 'user' => $db ['dbuser'], 'password' => $db ['passwd'], 'dbname' => $db ['dbname'] );
            $settings [DATABASE] = array ('default' => $db_default );
        }
    }
    /**
     * 
     * 安装任务列表
     * @return array
     */
    public function get_install_taskes() {
        $taskes = array ();
        $taskes [] = array ('text' => '获取预装模块', 'step' => 'cm', 'weight' => 5 );
        $taskes [] = array ('text' => '创建管理员' . $_SESSION ['_INSTALL_ADMIN_DATA'] ['name'], 'step' => 'cu', 'weight' => 5 );
        $taskes [] = array ('text' => '保存配置文件', 'step' => 'save', 'weight' => 5 );
        return $taskes;
    }
    /**
     * 
     * 获取要安装的所有模块
     * @return boolean
     */
    public function get_modules() {
        $modules = array ('cn.usephp.core.gui' => 1 );
        $profile = ProfileManager::getInstallProfile ();
        $third_mds = $profile->onInstallModules ();
        if (is_array ( $third_mds )) {
            $modules += $third_mds;
        }
        $count = floor ( 80.0 / count ( $modules ) );
        $em = ExtensionManager::getInstance ();
        $em->getExtensions ( false );
        foreach ( $modules as $module => $unremoveble ) {
            if (is_numeric ( $module )) {
                $module = $unremoveble;
                $unremoveble = 0;
            } else {
                $unremoveble = intval ( $unremoveble );
            }
            $md = $em->getExension ( $module );
            if ($md) {
                $taskes [] = array ('text' => '安装模块:' . $md ['Module_Name'], 'step' => 'cm', 'arg' => $unremoveble . $module, 'weight' => $count );
            }
        }
        return $taskes;
    }
    /**
     * 
     * 安装模块
     * @param string $mid
     * @return boolean
     */
    public function install_module($mid) {
        $em = ExtensionManager::getInstance ();
        $em->getExtensions ( false );
        $unremoveble = $mid {0};
        $mid = substr ( $mid, 1 );
        $rst = $em->installExtension ( $mid, $unremoveble );
        if ($rst === true) {
            return true;
        } else {
            $this->error = $rst;
            return false;
        }
    }
    public function create_administrator() {
        imports ( 'admin/models/*' );
        $admin = new CoreUserTable ();
        $tmp = $_SESSION ['_INSTALL_ADMIN_DATA'];
        $rst = false;
        do {
            try {
                $data = array ();
                //create super administrator
                $data ['uid'] = 1;
                $data ['login'] = $tmp ['name'];
                $data ['username'] = '超级管理员';
                $data ['email'] = $tmp ['email'];
                $data ['passwd'] = md5 ( $tmp ['passwd'] );
                $data ['reserved'] = true;
                $rst = $admin->save ( $data );
                if (count ( $rst ) == 0) {
                    break;
                }
                $role = new CoreRoleTable ();
                $data = array ('rid' => 1, 'label' => 'Administrator', 'name' => '超级管理员', 'reserved' => 1 );
                $rst = $role->save ( $data );
                if (count ( $rst ) == 0) {
                    break;
                }
                $userRole = new CoreUserRoleTable ();
                $data = array ('rid' => 1, 'uid' => 1 );
                $rst = $userRole->save ( $data );
                if (count ( $rst ) == 0) {
                    break;
                }
                // assign access policy to administrator
                $ap = new CoreAccessPolicyTable ();
                $data = array ('atype' => 'ROLE', 'aid' => 1, 'resource' => '*', 'action' => '*', 'allow' => true );
                $rst = $ap->save ( $data );
                if (count ( $rst ) == 0) {
                    break;
                }
                $data = array ('atype' => 'USER', 'aid' => 1, 'resource' => '*', 'action' => '*', 'allow' => true );
                $rst = $ap->save ( $data );
                if (count ( $rst ) == 0) {
                    break;
                }
                $rst = true;
            } catch ( PDOException $e ) {
                $rst = $e->getMessage ();
            }
        } while ( 0 );
        return $rst;
    }
    public function create_settings_file() {
        $dbForm = new InstallDbForm ( $_SESSION ['_INSTALL_DB_DATA'] );
        $db = $dbForm->validate ( null, true );
        if (! $db) {
            $this->error = '数据库配置有错,请检查.';
            return false;
        }
        $configForm = new InstallConfigForm ( $_SESSION ['_INSTALL_CONFIG_DATA'] );
        $config = $configForm->validate ( null, true );
        if (! $config) {
            $this->error = '基本配置有错误,请检查.';
            return false;
        }
        $rst = save_setting_to_file ( APPDATA_PATH . 'settings.php' );
        if ($rst === true) {
            return true;
        }
        $this->error = $rst;
        return false;
    }
    /**
     * 检测目录读写权限
     * @return array
     */
    public function check_directory_rw() {
        $uploads = (defined ( 'UPLOAD_DIR' ) && UPLOAD_DIR ? UPLOAD_DIR : 'uploads');
        $dirs = array ('appdata' => APPDATA_PATH, 'logs' => APPDATA_PATH . 'logs', 'tmp' => TMP_PATH, 'uploads' => WEB_ROOT . $uploads );
        $profile = ProfileManager::getInstallProfile ();
        $profile->onCheckDirectory ( $dirs );
        $rst = array ();
        foreach ( $dirs as $dir => $path ) {
            $r = is_readable ( $path );
            $len = @file_put_contents ( $path . 'test.dat', 'test' );
            $w = $len > 0;
            $rt = array ('dir' => $dir, 'path' => $path );
            $rx = $r ? '<span class="label label-success mr10">可读</span>' : '<span class="label label-important">不可读</span>';
            if ($w) {
                $wx = '<span class="label label-success mr10">可写</span>';
                @unlink ( $path . 'test.dat' );
            } else {
                $wx = '<span class="label label-important">不可写</span>';
            }
            if (! $w || ! $r) {
                $rt ['cls'] = 'error';
            } else {
                $rt ['cls'] = 'success';
            }
            $rt ['status'] = $rx . $wx;
            $rst [] = $rt;
        }
        return $rst;
    }
    public function check_server_env() {
        $envs = array ();
        $env = array ();
        $env ['name'] = 'PHP';
        $env ['requirement'] = '5.2+';
        $env ['current'] = phpversion ();
        $env ['cls'] = version_compare ( '5.2', phpversion (), '<=' ) ? 'success' : 'error';
        $envs [] = $env;
        //mysql
        $env ['name'] = 'MySQL';
        $env ['requirement'] = '有';
        if (function_exists ( 'mysql_query' )) {
            $env ['current'] = '<span class="label label-success mr10">有</span>';
            $env ['cls'] = 'success';
        } else {
            $env ['current'] = '<span class="label label-important">无</span>';
            $env ['cls'] = 'error';
        }
        $envs [] = $env;
        // pdo_mysql
        $env ['name'] = 'pdo_mysql';
        $env ['requirement'] = '可选';
        if (extension_loaded ( 'pdo_mysql' )) {
            $env ['current'] = '<span class="label label-success mr10">有</span>';
            $env ['cls'] = 'success';
        } else {
            $env ['current'] = '<span class="label label-important">无</span>';
            $env ['cls'] = 'warning';
        }
        $envs [] = $env;
        // pdo_pgsql
        $env ['name'] = 'pdo_pgsql';
        $env ['requirement'] = '可选';
        if (extension_loaded ( 'pdo_pgsql' )) {
            $env ['current'] = '<span class="label label-success mr10">有</span>';
            $env ['cls'] = 'success';
        } else {
            $env ['current'] = '<span class="label label-important">无</span>';
            $env ['cls'] = 'warning';
        }
        $envs [] = $env;
        // gd
        $env ['name'] = 'GD';
        $env ['requirement'] = '有';
        if (extension_loaded ( 'gd' )) {
            $env ['current'] = '<span class="label label-success mr10">有</span>';
            $env ['cls'] = 'success';
        } else {
            $env ['current'] = '<span class="label label-important">无</span>';
            $env ['cls'] = 'error';
        }
        $envs [] = $env;
        // json
        $env ['name'] = 'json';
        $env ['requirement'] = '有';
        if (function_exists ( 'json_encode' )) {
            $env ['current'] = '<span class="label label-success mr10">有</span>';
            $env ['cls'] = 'success';
        } else {
            $env ['current'] = '<span class="label label-important">无</span>';
            $env ['cls'] = 'error';
        }
        $envs [] = $env;
        // mb_string
        $env ['name'] = 'mb_string';
        $env ['requirement'] = '可选';
        if (function_exists ( 'mb_internal_encoding' )) {
            $env ['current'] = '<span class="label label-success mr10">有</span>';
            $env ['cls'] = 'success';
        } else {
            $env ['current'] = '<span class="label label-important">无</span>';
            $env ['cls'] = 'warning';
        }
        $envs [] = $env;
        
        $profile = ProfileManager::getInstallProfile ();
        $profile->onCheckServerEnv ( $envs );
        
        return $envs;
    }
    public function check_connection($config) {
        $ds = $this->getDs ( $config );
        if (! $ds) {
            return false;
        }
        return true;
    }
    private function getDs() {
        try {
            $ds = PdoDialect::getDialect ();
            return $ds;
        } catch ( PDOException $e ) {
            $this->error = $e->getMessage ();
            return false;
        }
    }
}
/**
 * 
 * 创建管理员表单
 * @author Leo Ning
 *
 */
class InstallAdminForm extends BootstrapForm {
    var $name = array (FWT_LABEL => '管理员账号', FWT_TIP => '此用户为超级管理员，可对系统进行维护。', FWT_VALIDATOR => array ('required' => '管理员账号不能为空.', 'minlength(4)' => '长度至少4个字符.', 'maxlength(15)' => '长度不能大于15个字条.', 'regexp(/^[a-z][a-z0-9_\\.]{3,14}$/i)' => '账号不能含有特殊字符' ), FWT_INITIAL => 'root' );
    var $passwd = array (FWT_WIDGET => 'password', FWT_LABEL => '登录密码', FWT_TIP => '请尽量设置复杂一点的密码.', FWT_VALIDATOR => array ('required' => '密码不能为空.', 'minlength(6)' => '密码至少6个字符.', 'maxlength(15)' => '密码最多15个字符.' ) );
    var $email = array (FWT_LABEL => '邮箱', FWT_TIP => '一些相关的信息将发到此邮箱.', FWT_VALIDATOR => array ('required' => '邮箱不能为空.', 'email' => '邮箱地址不合法.' ) );
    protected function getDefaultWidgetOptions() {
        return array (FWT_TIP_SHOW => FWT_TIP_SHOW_S, FWT_OPTIONS => array ('class' => 'input-xlarge' ) );
    }
}
/**
 * 
 * 数据库配置表单
 * @author Leo Ning
 *
 */
class InstallDbForm extends BootstrapForm {
    var $driver = array (FWT_WIDGET => 'select', FWT_LABEL => '数据库驱动', FWT_TIP => '使用何种方式访问数据库.', FWT_OPTIONS => array ('class' => 'span2' ), FWT_BIND => '@getDrivers', FWT_INITIAL => 'mysql', FWT_TIP_SHOW => FWT_TIP_SHOW_S, FWT_NO_APPLY => true );
    var $host = array (FWT_LABEL => '主机地址', FWT_TIP => '使用何种方式访问数据库.', FWT_INITIAL => 'localhost', FWT_VALIDATOR => array ('required' => '主机地址必须填写.' ) );
    var $port = array (FWT_LABEL => '端口', FWT_TIP => '数据库服务器使用的端口.', FWT_INITIAL => '3306', FWT_VALIDATOR => array ('required' => '端口必须填写.', 'num' => '端口只能是数字.' ) );
    var $dbuser = array (FWT_LABEL => '数据库用户', FWT_TIP => '可以访问数据库的用户.', FWT_INITIAL => 'root', FWT_VALIDATOR => array ('required' => '数据库用户名不能为空' ) );
    var $passwd = array (FWT_WIDGET => 'password', FWT_LABEL => '用户的密码', FWT_TIP => '可以访问数据库的用户的密码.', FWT_VALIDATOR => array ('required' => '用户的密码不能为空' ) );
    var $dbname = array (FWT_LABEL => '数据库', FWT_TIP => 'KissGO!将要使用的数据库.', FWT_INITIAL => 'kissgodb', FWT_VALIDATOR => array ('required' => '数据库不能为空' ) );
    var $prefix = array (FWT_LABEL => '表前缀', FWT_TIP => '在一个库中安装多个KissGO时,请指定前缀,例如:app_', FWT_VALIDATOR => array ('regexp(/^[a-z][\w\d]*_$/i)' => '表前缘格式错误,必须以字母开头下划线结尾.' ) );
    var $engine = array (FWT_WIDGET => 'select', FWT_LABEL => '存储引擎', FWT_TIP => '仅当数据库驱动为MySQL时有效.', FWT_OPTIONS => array ('class' => 'span2' ), FWT_INITIAL => 'MyISAM', FWT_BIND => '@getEngines', FWT_TIP_SHOW => FWT_TIP_SHOW_S, FWT_NO_APPLY => true );
    protected function getDefaultWidgetOptions() {
        return array (FWT_TIP_SHOW => FWT_TIP_SHOW_S, FWT_OPTIONS => array ('class' => 'input-xlarge' ) );
    }
    public function getDrivers($value, $data) {
        $drivers = array ('mysql' => 'MySQL', 'psgl' => 'PostgreSQL' );
        if (! extension_loaded ( 'pdo_pgsql' )) {
            unset ( $drivers ['psgl'] );
        }
        if (! extension_loaded ( 'pdo_mysql' )) {
            unset ( $drivers ['mysql'] );
        }
        return $drivers;
    }
    public function getEngines($value, $data) {
        $engines = array ('InnoDB' => 'InnoDB', 'MyISAM' => 'MyISAM', 'NDB' => 'NDB' );
        return $engines;
    }
}
/**
 * 
 * 配置表单
 * @author Leo Ning
 *
 */
class InstallConfigForm extends BootstrapForm {
    var $site_name = array (FWT_LABEL => '网站名称', FWT_TIP => '网站的主要名称.', FWT_INITIAL => '我的网站', FWT_VALIDATOR => array ('required' => '网站名称不能为空，必须填写.' ) );
    var $security_key = array (FWT_LABEL => '安全码', FWT_TIP => '用于加密一些比较敏感的COOKIE数据或加密与其它服务器交换的数.', FWT_INITIAL_FUN => '@getSecurityCode', FWT_VALIDATOR => array ('required' => '安全码不能为空，必须填写.', 'minlength(32)' => '为了保证安全,安全码长度不能小于32个字符.' ), FWT_OPTIONS => array ('class' => 'input-xxlarge' ), FWT_TIP_SHOW => FWT_TIP_SHOW_T, FWT_NO_APPLY => true );
    var $gzip = array (FWT_WIDGET => 'scheckbox', FWT_LABEL => '启用GZIP压缩', FWT_TIP => '启用GZIP压缩以节省网络流利与加快传输速度.' );
    var $clean_url = array (FWT_WIDGET => 'scheckbox', FWT_LABEL => '启用伪静态', FWT_TIP => '需要服务器支持.', FWT_INITIAL_FUN => '@isSupportedCleanURL' );
    var $i18n = array (FWT_WIDGET => 'scheckbox', FWT_LABEL => '启用多语言支持' );
    var $timezone = array (FWT_WIDGET => 'select', FWT_LABEL => '选择时区', FWT_TIP => '选择系统将使用的时区.', FWT_OPTIONS => array ('class' => 'span2' ), FWT_INITIAL => 'Asia/Shanghai', FWT_BIND => '@getTimezones' );
    var $date_format = array (FWT_WIDGET => 'select', FWT_LABEL => '日期格式', FWT_TIP => '系统将以此格式显示日期.', FWT_OPTIONS => array ('class' => 'span2' ), FWT_INITIAL => 'Y-m-d', FWT_BIND => '@getDateFormats' );
    var $debug = array (FWT_WIDGET => 'select', FWT_LABEL => '调试级别', FWT_TIP => '控制系统日志记录级别.', FWT_OPTIONS => array ('class' => 'span3' ), FWT_INITIAL => '4', FWT_BIND => '@getDebugLevels' );
    protected function getDefaultWidgetOptions() {
        return array (FWT_TIP_SHOW => FWT_TIP_SHOW_S, FWT_OPTIONS => array ('class' => 'input-xlarge' ) );
    }
    public function getTimezones($value, $data) {
        $timezones = array ('Asia/Shanghai' => 'Asia/Shanghai' );
        return $timezones;
    }
    public function getDateFormats($value, $data) {
        $formats = array ('Y-m-d' => '年-月-日', 'm/d/y' => '月/日/年' );
        return $formats;
    }
    public function getDebugLevels() {
        $levels = array (DEBUG_DEBUG => '调试(记录所有日志)', DEBUG_WARN => '警告(记录除调试之外的日志)', DEBUG_INFO => '信息(记录信息与错误日志)', DEBUG_ERROR => '错误(仅记录错误日志)' );
        return $levels;
    }
    public function getSecurityCode() {
        return randstr ( 48 );
    }
    public function isSupportedCleanURL() {
        $headers = get_headers ( detect_app_base_url ( true ) . 'install.test.clean.url' );
        if (preg_match ( '#.*OK$#', $headers [0] ))
            return 1;
        return 0;
    }
    public function init_clean_url(&$widget, &$value) {
        if (! $value) {
            $widget [FWT_OPTIONS] ['disabled'] = 'disabled';
            $widget [FWT_TIP] = '您的服务器不支持伪静态.';
        } else {
            $widget [FWT_TIP] = '您的服务器支持伪静态,建议开启.';
        }
    }
}
//end of install.php file