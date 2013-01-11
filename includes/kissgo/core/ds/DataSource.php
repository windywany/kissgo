<?php
/**
 * 抽象的数据库访问适配器接口
 *
 * 定义每个适配器
 *
 * @property string error_msg
 * @property mixed queryString
 * @author Leo Ning <leo.ning@like18.com>
 * @abstract
 * @version 1.0
 * @since 1.0
 * @copyright 2008-2011 LIKE18 INC.
 * @package model
 */
abstract class DataSource {
    protected $options = array ();
    protected $charset = 'UTF8';
    protected $connection = NULL;
    private static $queryStrings = array ();
    private $error_message;
    public static $errors = array ();
    /**
     * 构造函数,只负责为子类接收参数
     *
     * @param array $options
     * 数据源参数(选项)
     */
    public function __construct($options = array()) {
        $this->options = $options;
    }
    
    public function __set($name, $value) {
        if ($name == 'queryString') {
            self::$queryStrings [] = $value;
        } else if ($name == 'error_msg') {
            $this->error_message = $value;
            log_debug ( $value );
        }
    }
    public function __get($name) {
        if ($name == 'error_msg') {
            return $this->error_message;
        }
    }
    /**
     * 得到本次执行中所有的SQL语句
     *
     * @return array
     */
    public static function getSqls() {
        return self::$queryStrings;
    }
    
    /**
     * 取数据源实例
     * @param string $datasource 得到数据源
     * @return DataSource
     */
    public static function getDataSource($datasource = 'default') {
        static $ds = array ();
        if (! isset ( $ds [$datasource] )) {
            $settings = KissGoSetting::getSetting ();
            if (! isset ( $settings [DATABASE] )) {
                trigger_error ( '[' . $datasource . ']数据库配置不存在.', E_USER_ERROR );
            }
            $database_settings = $settings [DATABASE];
            if (! isset ( $database_settings [$datasource] )) {
                trigger_error ( '[' . $datasource . ']数据库配置不存在.', E_USER_ERROR );
            }
            $options = $database_settings [$datasource];
            $driver = isset ( $options ['driver'] ) && ! empty ( $options ['driver'] ) ? $options ['driver'] : 'Mysql';
            $driver .= 'Datasource';
            if (! is_subclass_of ( $driver, 'DataSource' )) {
                trigger_error ( '[' . $datasource . ']数据库驱动器' . $driver . '不存在.', E_USER_ERROR );
            }
            $dr = new $driver ( $options );
            if (! $dr->connect ()) {
                trigger_error ( '[' . $datasource . ']数据库驱动器报错:' . $dr->last_error_msg (), E_USER_ERROR );
            }
            $ds [$datasource] = $dr;
        }
        return $ds [$datasource];
    }
    /**
     * 连接数据库
     *
     * @return boolean 成功返回true,失败返回false
     */
    public abstract function connect();
    
    /**
     * 表全名,即添加前缀的后的名称
     *
     * @param $table
     * @return
     * @internal param string $tablename 表名称
     */
    public abstract function full_name($table);
    
    /**
     * 选择数据库
     *
     * @param string $db
     * 要选择的数据库
     * @return boolean 成功true,失败false
     */
    public abstract function usedb($db);
    
    /**
     * 查询操作
     *
     * @param string $sql
     * 要查询的语句
     * @return ResultSet 查询结果集,失败返回false
     */
    public abstract function query($sql);
    
    /**
     * 分页查询
     *
     * @param string $sql
     * 语句
     * @param int $start
     * 分页
     * @param int $limit
     * 分页大小
     * @return ResultSet 查询结果集
     */
    public abstract function queryLimit($sql, $start, $limit);
    
    /**
     * 执行非查询SQL语句
     *
     * @param string $sql
     * 要执行的SQL语句
     * @return int 操作影响的行数
     */
    public abstract function execute($sql);
    
    /**
     * 最后一次生成的主键值
     *
     * @param string $name
     * @return int
     */
    public abstract function last_insert_id($name = '');
    
    /**
     * 开始事务
     *
     * @return boolean 事务是否开始成功
     */
    public abstract function begin();
    
    /**
     * 提交事务
     *
     * @return boolean 提交是否成功
     */
    public abstract function commit();
    
    /**
     * 回滚事务
     *
     * @return boolean 回滚是否成功
     */
    public abstract function rollback();
    
    /**
     * 转义字字符
     *
     * @param string $str
     * 要转义的字符
     * @return string 已经转义的字符
     */
    public abstract function escape($str);    
    /**
     * 设置连接字符编码
     *
     * @param string $encoding
     * 编码
     */
    public function encoding($encoding = '') {
        $this->charset = $encoding;
    }
    
    /**
     * 返回最近一次错误信息
     *
     * @param string $msg
     * @return string|null 错误信息
     */
    public function last_error_msg($msg = '') {
        if (! empty ( $msg )) {
            $this->error_msg = $msg;
        }
        return $this->error_message;
    }
    
    /**
     * 最后一次执行的SQL语句.
     *
     * @return string 数据源最后一次执行的SQL语句
     */
    public function last_query_string() {
        return $this->queryString;
    }
    
    /**
     * 断开与数据库的连接
     */
    public function disconnect() {
        return true;
    }
}