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
/**
 * 
 * 安装任务列表
 * @return array
 */
function get_install_taskes() {
    $taskes = array ();
    $taskes [] = array (
                        
                        'text' => '获取数据表列表', 
                        'step' => 'scheme', 
                        'weight' => 5 
    );
    $taskes [] = array (
                        
                        'text' => '创建管理员', 
                        'step' => 'cu', 
                        'weight' => 15 
    );
    $taskes [] = array (
                        
                        'text' => '创建settings.php文件', 
                        'step' => 'save', 
                        'weight' => 15 
    );
    return $taskes;
}
/**
 * 
 * 得到要安装的表
 */
function get_scheme_tables() {
    $taskes = array ();
    $taskes [] = array (
                        
                        'text' => '创建表:abc', 
                        'step' => 'scheme', 
                        'arg' => 'abc', 
                        'weight' => 20 
    );
    $taskes [] = array (
                        
                        'text' => '创建表:def', 
                        'step' => 'scheme', 
                        'arg' => 'abc', 
                        'weight' => 20 
    );
    $taskes [] = array (
                        
                        'text' => '创建表:ghi', 
                        'step' => 'scheme', 
                        'arg' => 'abc', 
                        'weight' => 20 
    );
    return $taskes;
}
/**
 * 检测目录读写权限
 * @return array
 */
function check_directory_rw() {
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
function check_server_env() {
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
                                            'required', 
                                            'minlength(4)', 
                                            'maxlength(15)' 
                    ), 
                    FWT_INITIAL => 'root' 
    );
    var $passwd = array (
                        FWT_LABEL => '登录密码', 
                        FWT_TIP => '请尽量设置复杂一点的密码.', 
                        FWT_VALIDATOR => array (
                                                'required', 
                                                'minlength(6)', 
                                                'maxlength(15)' 
                        ) 
    );
    var $email = array (
                        FWT_LABEL => '邮箱', 
                        FWT_TIP => '一些相关的信息将发到此邮箱.', 
                        FWT_VALIDATOR => array (
                                                'required', 
                                                'email' 
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
                                            'required' 
                    ) 
    );
    var $port = array (
                    FWT_LABEL => '端口', 
                    FWT_TIP => '数据库服务器使用的端口,如果使用默认值请留空.', 
                    FWT_INITIAL => '', 
                    FWT_VALIDATOR => array (
                                            'num' 
                    ) 
    );
    var $dbuser = array (
                        FWT_LABEL => '数据库用户', 
                        FWT_TIP => '可以访问数据库的用户.', 
                        FWT_INITIAL => 'root', 
                        FWT_VALIDATOR => array (
                                                'required' 
                        ) 
    );
    var $passwd = array (
                        FWT_LABEL => '用户的密码', 
                        FWT_TIP => '可以访问数据库的用户的密码.', 
                        FWT_VALIDATOR => array (
                                                'required' 
                        ) 
    );
    var $dbname = array (
                        FWT_LABEL => '数据库', 
                        FWT_TIP => 'KissGO!将要使用的数据库.', 
                        FWT_INITIAL => 'kissgodb', 
                        FWT_VALIDATOR => array (
                                                'required' 
                        ) 
    );
    var $prefix = array (
                        FWT_LABEL => '表前缀', 
                        FWT_TIP => '如果要在一个库中安装多个KissGO时，请指定一个前缀.' 
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
    public function check_connection($config) {
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
        if (! $ds) {            
            return false;
        }        
        return true;
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
                                                    'required' 
                            ) 
    );
    var $security_key = array (
                            FWT_LABEL => '安全码', 
                            FWT_TIP => '用于加密一些比较敏感的COOKIE数据或加密与其它服务器交换的数.', 
                            FWT_INITIAL => '', 
                            FWT_VALIDATOR => array (
                                                    'required' 
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
}
//end of install.php file