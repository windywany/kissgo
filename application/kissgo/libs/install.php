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
    private $createAminSQL = "INSERT INTO `%PREFIX%user` VALUES ";
    private $createAuthSQL = "INSERT INTO `%PREFIX%authorization` VALUES (1,'USER',1,'*','*',100,1,NULL)";
    private $createPfSQL = "INSERT INTO `%PREFIX%preference` (option_group,option_name,option_value) VALUES ";
    /**
     * 
     * 安装任务列表
     * @return array
     */
    public function get_install_taskes() {
        $taskes = array ();
        $taskes [] = array (
                            'text' => '获取数据表列表', 
                            'step' => 'scheme', 
                            'weight' => 6 
        );
        $taskes [] = array (
                            'text' => '创建管理员:' . $_SESSION ['_INSTALL_ADMIN_DATA'] ['name'], 
                            'step' => 'cu', 
                            'weight' => 7 
        );
        $taskes [] = array (
                            'text' => '保存配置信息', 
                            'step' => 'pf', 
                            'weight' => 7 
        );
        $taskes [] = array (
                            'text' => '安装核心模块', 
                            'step' => 'cm', 
                            'weight' => 10 
        );
        $taskes [] = array (
                            'text' => '创建settings.php文件', 
                            'step' => 'save', 
                            'weight' => 5 
        );
        return $taskes;
    }
    /**
     * 
     * 得到要安装的表
     */
    public function get_scheme_tables() {
        $taskes = array ();
        require_once KISSGO . 'libs/install_core_scheme.php';
        $scheme = KissGoSetting::getSetting ( 'scheme' );
        $schemes = $scheme->toArray ();
        $count = 60 / count ( $schemes );
        foreach ( $schemes as $table => $sql ) {
            $taskes [] = array (
                                'text' => '创建系统核心表:' . $table, 
                                'step' => 'scheme', 
                                'arg' => $table, 
                                'weight' => $count 
            );
        }
        return $taskes;
    }
    public function create_scheme_table($name) {
        $dbConfig = $_SESSION ['_INSTALL_DB_DATA'];
        $ds = $this->getDs ( $dbConfig );
        if ($ds) {
            require_once KISSGO . 'libs/install_core_scheme.php';
            $scheme = KissGoSetting::getSetting ( 'scheme' );
            $sql = isset ( $scheme [$name] ) ? $scheme [$name] : false;
            if ($sql) {
                $sql = str_replace ( array (
                                            '%PREFIX%', 
                                            '%ENGINE%' 
                ), array (
                        $dbConfig ['prefix'], 
                        $dbConfig ['engine'] 
                ), $sql );
                $rst = $ds->execute ( "DROP TABLE IF EXISTS `{$dbConfig['prefix']}{$name}`" );
                if ($rst === false) {
                    $this->error = $ds->last_error_msg ();
                    return false;
                }
                $rst = $ds->execute ( $sql );
                if ($rst !== false) {
                    return true;
                } else {
                    $this->error = $ds->last_error_msg ();
                    return false;
                }
            } else {
                $this->error = '建表语句不存在.';
                return false;
            }
        } else {
            $this->error = DataSource::getLastError ();
            return false;
        }
    }
    public function create_administrator() {
        $dbConfig = $_SESSION ['_INSTALL_DB_DATA'];
        $ds = $this->getDs ( $dbConfig );
        if ($ds) {
            $admin = $_SESSION ['_INSTALL_ADMIN_DATA'];
            $passwd = md5 ( $admin ['passwd'] );
            $time = time ();
            $sql = str_replace ( '%PREFIX%', $dbConfig ['prefix'], $this->createAminSQL );
            $sql .= "(1,'{$admin['name']}','{$passwd}','Administrator','{$admin['email']}',0,1,{$time},'127.0.0.1')";
            
            $rst = $ds->execute ( $sql );
            if ($rst == 1) {
                $sql = str_replace ( '%PREFIX%', $dbConfig ['prefix'], $this->createAuthSQL );
                $rst = $ds->execute ( $sql );
                if ($rst == 1) {
                    return true;
                }
            }
            $this->error = $ds->last_error_msg ();
            return false;
        } else {
            $this->error = DataSource::getLastError ();
            return false;
        }
    }
    public function save_peferences() {
        $dbConfig = $_SESSION ['_INSTALL_DB_DATA'];
        $ds = $this->getDs ( $dbConfig );
        if ($ds) {
            $settings = KissGoSetting::getSetting ();
            $data [] = array (
                            'name' => 'FULL_VERSION', 
                            'value' => $settings ['VERSION'] . ' ' . $settings ['RELEASE'] . ' BUILD ' . $settings ['BUILD'] 
            );
            $data [] = array (
                            'name' => 'VERSION', 
                            'value' => $settings ['VERSION'] 
            );
            $data [] = array (
                            'name' => 'RELEASE', 
                            'value' => $settings ['RELEASE'] 
            );
            $data [] = array (
                            'name' => 'BUILD', 
                            'value' => $settings ['BUILD'] 
            );
            $data [] = array (
                            'name' => 'R_VERSION', 
                            'value' => $settings ['VERSION'] . ' ' . $settings ['RELEASE'] 
            );
            $data [] = array (
                            'name' => 'su', 
                            'value' => '1' 
            );
            $data [] = array (
                            'name' => 'time', 
                            'value' => time () 
            );
            
            $sqls = array ();
            foreach ( $data as $option ) {
                $sqls [] = "('core','{$option['name']}','{$option['value']}')";
            }
            $sql = str_replace ( '%PREFIX%', $dbConfig ['prefix'], $this->createPfSQL ) . implode ( ',', $sqls );
            $rst = $ds->execute ( $sql );
            if ($rst > 0) {
                return true;
            }
            $this->error = $ds->last_error_msg ();
            return false;
        } else {
            $this->error = DataSource::getLastError ();
            return false;
        }
    }
    public function install_core_modules() {
        $plgmgr = PluginManager::getInstance ();
        $ext = $plgmgr->getExensionInfo ( KISSGO . 'modules/kissgo/__pkg__.php', 1 );
        $ext ['unremovable'] = 1;
        $ext ['disabled'] = 0;
        $ext ['Installed_Time'] = time ();
        unset ( $ext ['Installed'] );
        $extensions [] = $ext;
        $ext = $plgmgr->getExensionInfo ( KISSGO . 'modules/passport/__pkg__.php', 1 );
        $ext ['unremovable'] = 1;
        $ext ['disabled'] = 0;
        $ext ['Installed_Time'] = time ();
        unset ( $ext ['Installed'] );
        $extensions [] = $ext;
        if ($plgmgr->saveExtensionsData ( $extensions )) {
            return true;
        } else {
            $this->error = '无法写入配置文件：[' . APPDATA_PATH . 'extensions.ini]. 请检查目录是否有可写权限.';
            return false;
        }
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
        $file = "<?php\n//generated by kissgo,don't edit this file manually!\ndefined('KISSGO') or exit('No direct script access allowed');\n";
        $file .= "\$settings = KissGoSetting::getSetting();\n";
        $file .= "\$settings[DEBUG] = {$config['debug']};\n";
        
        $bool = $config ['clean_url'] ? 'true' : 'false';
        $file .= "\$settings[CLEAN_URL] = {$bool};\n";
        
        $bool = $config ['i18n'] ? 'true' : 'false';
        $file .= "\$settings[I18N_ENABLED] = {$bool};\n";
        
        $bool = $config ['gzip'] ? 'true' : 'false';
        $file .= "\$settings[GZIP_ENABLED] = {$bool};\n";
        
        $file .= "\$settings[TIMEZONE] = '{$config['timezone']}';\n";
        $file .= "\$settings[SECURITY_KEY] = '{$config['security_key']}';\n";
        $file .= "\$settings['date_format'] = '{$config['date_format']}';\n";
        $file .= "\$settings['site_name'] = '{$config['site_name']}';\n";
        
        $db_default = "array('driver'=>'{$db['driver']}','encoding' => 'UTF8','pconnect' => false,'host'=>'{$db['host']}','port'=>{$db['port']},'prefix'=>'{$db['prefix']}','user'=>'{$db['dbuser']}','password'=>'{$db['passwd']}','dbname'=>'{$db['dbname']}')";
        $file .= "\$settings[DATABASE] = array('default'=>{$db_default});\n";
        $file .= "// end of settings.php\n?>";
        $rst = @file_put_contents ( APPDATA_PATH . 'settings.php', $file );
        if ($rst !== false) {
            return true;
        }
        $this->error = '无法写入配置文件：[' . APPDATA_PATH . 'settings.php]. 请检查目录是否有可写权限.';
        return false;
    }
    /**
     * 检测目录读写权限
     * @return array
     */
    public function check_directory_rw() {
        $dirs = array (
                    'appdata' => APPDATA_PATH, 
                    'logs' => APPDATA_PATH . 'logs', 
                    'tmp' => TMP_PATH 
        );
        $rst = array ();
        foreach ( $dirs as $dir => $path ) {
            $r = is_readable ( $path );
            $len = @file_put_contents ( $path . 'test.dat', 'test' );
            $w = $len > 0;
            $rt = array (
                        'dir' => $dir, 
                        'path' => $path 
            );
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
        return $envs;
    }
    public function check_connection($config) {
        $ds = $this->getDs ( $config );
        if (! $ds) {
            return false;
        }
        return true;
    }
    private function getDs($config) {
        $settings = KissGoSetting::getSetting ();
        $settings [DATABASE] = array (
                                    'default' => array (
                                                        'driver' => $config ['driver'], 
                                                        'encoding' => 'UTF8', 
                                                        'prefix' => '', 
                                                        'host' => $config ['host'], 
                                                        'port' => $config ['port'], 
                                                        'user' => $config ['dbuser'], 
                                                        'password' => $config ['passwd'], 
                                                        'pconnect' => false, 
                                                        'dbname' => $config ['dbname'] 
                                    ) 
        );
        $ds = DataSource::getDataSource ();
        return $ds;
    }
}
/**
 * 
 * 创建管理员表单
 * @author Leo Ning
 *
 */
class InstallAdminForm extends BootstrapForm {
    var $name = array (
                    FWT_LABEL => '管理员账号', 
                    FWT_TIP => '此用户为超级管理员，可对系统进行维护。', 
                    FWT_VALIDATOR => array (
                                            'required' => '管理员账号不能为空.', 
                                            'minlength(4)' => '长度至少4个字符.', 
                                            'maxlength(15)' => '长度不能大于15个字条.', 
                                            'regexp(/^[a-z][a-z0-9_\\.]{3,14}$/i)' => '账号不能含有特殊字符' 
                    ), 
                    FWT_INITIAL => 'root' 
    );
    var $passwd = array (
                        FWT_WIDGET => 'password', 
                        FWT_LABEL => '登录密码', 
                        FWT_TIP => '请尽量设置复杂一点的密码.', 
                        FWT_VALIDATOR => array (
                                                'required' => '密码不能为空.', 
                                                'minlength(6)' => '密码至少6个字符.', 
                                                'maxlength(15)' => '密码最多15个字符.' 
                        ) 
    );
    var $email = array (
                        FWT_LABEL => '邮箱', 
                        FWT_TIP => '一些相关的信息将发到此邮箱.', 
                        FWT_VALIDATOR => array (
                                                'required' => '邮箱不能为空.', 
                                                'email' => '邮箱地址不合法.' 
                        ) 
    );
    protected function getDefaultWidgetOptions() {
        return array (
                    FWT_TIP_SHOW => FWT_TIP_SHOW_S, 
                    FWT_OPTIONS => array (
                                        'class' => 'input-xlarge' 
                    ) 
        );
    }
}
/**
 * 
 * 数据库配置表单
 * @author Leo Ning
 *
 */
class InstallDbForm extends BootstrapForm {
    var $driver = array (
                        FWT_WIDGET => 'select', 
                        FWT_LABEL => '数据库驱动', 
                        FWT_TIP => '使用何种方式访问数据库.', 
                        FWT_OPTIONS => array (
                                            'class' => 'span2' 
                        ), 
                        FWT_BIND => '@getDrivers', 
                        FWT_INITIAL => 'Mysql', 
                        FWT_TIP_SHOW => FWT_TIP_SHOW_S, 
                        FWT_NO_APPLY => true 
    );
    var $host = array (
                    FWT_LABEL => '主机地址', 
                    FWT_TIP => '使用何种方式访问数据库.', 
                    FWT_INITIAL => 'localhost', 
                    FWT_VALIDATOR => array (
                                            'required' => '主机地址必须填写.' 
                    ) 
    );
    var $port = array (
                    FWT_LABEL => '端口', 
                    FWT_TIP => '数据库服务器使用的端口.', 
                    FWT_INITIAL => '3306', 
                    FWT_VALIDATOR => array (
                                            'required' => '端口必须填写.', 
                                            'num' => '端口只能是数字.' 
                    ) 
    );
    var $dbuser = array (
                        FWT_LABEL => '数据库用户', 
                        FWT_TIP => '可以访问数据库的用户.', 
                        FWT_INITIAL => 'root', 
                        FWT_VALIDATOR => array (
                                                'required' => '数据库用户名不能为空' 
                        ) 
    );
    var $passwd = array (
                        FWT_WIDGET => 'password', 
                        FWT_LABEL => '用户的密码', 
                        FWT_TIP => '可以访问数据库的用户的密码.', 
                        FWT_VALIDATOR => array (
                                                'required' => '用户的密码不能为空' 
                        ) 
    );
    var $dbname = array (
                        FWT_LABEL => '数据库', 
                        FWT_TIP => 'KissGO!将要使用的数据库.', 
                        FWT_INITIAL => 'kissgodb', 
                        FWT_VALIDATOR => array (
                                                'required' => '数据库不能为空' 
                        ) 
    );
    var $prefix = array (
                        FWT_LABEL => '表前缀', 
                        FWT_TIP => '在一个库中安装多个KissGO时,请指定前缀,例如:app_', 
                        FWT_VALIDATOR => array (
                                                'regexp(/^[a-z][\w\d]*_$/i)' => '表前缘格式错误,必须以字母开头下划线结尾.' 
                        ) 
    );
    var $engine = array (
                        FWT_WIDGET => 'select', 
                        FWT_LABEL => '存储引擎', 
                        FWT_TIP => '如果你使用MySQL Cluster,请选择NDB.', 
                        FWT_OPTIONS => array (
                                            'class' => 'span2' 
                        ), 
                        FWT_INITIAL => 'MyISAM', 
                        FWT_BIND => '@getEngines', 
                        FWT_TIP_SHOW => FWT_TIP_SHOW_S, 
                        FWT_NO_APPLY => true 
    );
    protected function getDefaultWidgetOptions() {
        return array (
                    FWT_TIP_SHOW => FWT_TIP_SHOW_S, 
                    FWT_OPTIONS => array (
                                        'class' => 'input-xlarge' 
                    ) 
        );
    }
    public function getDrivers($value, $data) {
        $drivers = array (
                        'Mysql' => 'MySQL', 
                        'PdoMysql' => 'PDO MySQL' 
        );
        return $drivers;
    }
    public function getEngines($value, $data) {
        $engines = array (
                        'InnoDB' => 'InnoDB', 
                        'MyISAM' => 'MyISAM', 
                        'NDB' => 'NDB' 
        );
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
    var $site_name = array (
                            FWT_LABEL => '网站名称', 
                            FWT_TIP => '网站的主要名称.', 
                            FWT_INITIAL => '我的网站', 
                            FWT_VALIDATOR => array (
                                                    'required' => '网站名称不能为空，必须填写.' 
                            ) 
    );
    var $security_key = array (
                            FWT_LABEL => '安全码', 
                            FWT_TIP => '用于加密一些比较敏感的COOKIE数据或加密与其它服务器交换的数.', 
                            FWT_INITIAL => '', 
                            FWT_VALIDATOR => array (
                                                    'required' => '安全码不能为空，必须填写.', 
                                                    'minlength(32)' => '为了保证安全,安全码长度不能小于32个字符.' 
                            ), 
                            FWT_OPTIONS => array (
                                                'class' => 'input-xxlarge' 
                            ), 
                            FWT_TIP_SHOW => FWT_TIP_SHOW_T, 
                            FWT_NO_APPLY => true 
    );
    var $gzip = array (
                    FWT_WIDGET => 'scheckbox', 
                    FWT_LABEL => '启用GZIP压缩', 
                    FWT_TIP => '启用GZIP压缩以节省网络流利与加快传输速度.' 
    );
    var $clean_url = array (
                            FWT_WIDGET => 'scheckbox', 
                            FWT_LABEL => '启用重写', 
                            FWT_TIP => '需要服务器支持.' 
    );
    var $i18n = array (
                    FWT_WIDGET => 'scheckbox', 
                    FWT_LABEL => '启用多语言支持' 
    );
    var $timezone = array (
                        FWT_WIDGET => 'select', 
                        FWT_LABEL => '选择时区', 
                        FWT_TIP => '选择系统将使用的时区.', 
                        FWT_OPTIONS => array (
                                            'class' => 'span2' 
                        ), 
                        FWT_INITIAL => 'Asia/Shanghai', 
                        FWT_BIND => '@getTimezones' 
    );
    var $date_format = array (
                            FWT_WIDGET => 'select', 
                            FWT_LABEL => '日期格式', 
                            FWT_TIP => '系统将以此格式显示日期.', 
                            FWT_OPTIONS => array (
                                                'class' => 'span2' 
                            ), 
                            FWT_INITIAL => 'Y-m-d', 
                            FWT_BIND => '@getDateFormats' 
    );
    var $debug = array (
                        FWT_WIDGET => 'select', 
                        FWT_LABEL => '调试级别', 
                        FWT_TIP => '控制系统日志记录级别.', 
                        FWT_OPTIONS => array (
                                            'class' => 'span3' 
                        ), 
                        FWT_INITIAL => '4', 
                        FWT_BIND => '@getDebugLevels' 
    );
    protected function getDefaultWidgetOptions() {
        return array (
                    FWT_TIP_SHOW => FWT_TIP_SHOW_S, 
                    FWT_OPTIONS => array (
                                        'class' => 'input-xlarge' 
                    ) 
        );
    }
    public function getTimezones($value, $data) {
        $timezones = array (
                            'Asia/Shanghai' => 'Asia/Shanghai' 
        );
        return $timezones;
    }
    public function getDateFormats($value, $data) {
        $formats = array (
                        'Y-m-d' => '年-月-日', 
                        'm/d/y' => '月/日/年' 
        );
        return $formats;
    }
    public function getDebugLevels() {
        $levels = array (
                        DEBUG_DEBUG => '调试(记录所有日志)', 
                        DEBUG_WARN => '警告(记录除调试之外的日志)', 
                        DEBUG_INFO => '信息(记录信息与错误日志)', 
                        DEBUG_ERROR => '错误(仅记录错误日志)' 
        );
        return $levels;
    }
}
//end of install.php file