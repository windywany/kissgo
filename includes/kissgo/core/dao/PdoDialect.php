<?php
/**
 * 
 * database driver base class
 * @author guangfeng.ning
 *
 */
abstract class PdoDialect extends PDO {
    public static $last_error_message = '';
    protected $tbl_prefix = '';
    public function __construct($options) {
        list ( $dsn, $user, $passwd, $attr ) = $this->buildOptions ( $options );
        parent::__construct ( $dsn, $user, $passwd, $attr );
        $this->tbl_prefix = isset ( $options ['prefix'] ) && ! empty ( $options ['prefix'] ) ? $options ['prefix'] : '';
    }
    
    public function getFullTableName($name) {
        return $this->tbl_prefix . $name;
    }
    public function getTablePrefix() {
        return $this->tbl_prefix;
    }
    /**
     * 
     * 获取数据库驱动
     * @param string $driver
     * @return PdoDialect
     */
    public static function getDialect($driver = 'default') {
        static $ds = array ();
        if (! isset ( $ds [$driver] )) {
            $settings = KissGoSetting::getSetting ();
            if (! isset ( $settings [DATABASE] )) {
                trigger_error ( '[' . $driver . ']数据库配置不存在.', E_USER_ERROR );
            }
            $database_settings = $settings [DATABASE];
            if (! isset ( $database_settings [$driver] )) {
                trigger_error ( '[' . $driver . ']数据库配置不存在.', E_USER_ERROR );
            }
            $options = $database_settings [$driver];
            $driver_path = isset ( $options ['driver'] ) && ! empty ( $options ['driver'] ) ? $options ['driver'] : 'mysql';
            $driverClz = ucfirst ( $driver_path ) . 'PdoDialect';
            $driverFile = dirname ( __FILE__ ) . DS . $driver_path . DS . $driverClz . '.php';
            if (! is_file ( $driverFile )) {
                trigger_error ( '[' . $driver . ']数据库驱动器' . $driver_path . '实现文件' . $driverFile . '不存在.', E_USER_ERROR );
            }
            include_once $driverFile;
            if (! is_subclass_of2 ( $driverClz, 'PdoDialect' )) {
                trigger_error ( '[' . $driver . ']数据库驱动器' . $driver_path . '不存在.', E_USER_ERROR );
            }
            $dr = new $driverClz ( $options );
            $dr->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $ds [$driver] = $dr;
        }
        return $ds [$driver];
    }
    /**
     * 
     * create table
     * @param Idao $dao
     * @param string $database
     * @return boolean
     */
    public static function createTable($dao, $database = 'default', $engine = null) {
        $sql = '';
        try {
            $driver = PdoDialect::getDialect ( $database );
            $sql = $dao->getCreateSql ();
            if (empty ( $sql )) {
                $fields = array ();
                $builder = $driver->getSqlBuilder ();
                if ($engine == null) {
                    $engine = isset ( $_SESSION ['_INSTALL_DB_DATA'] ) && $_SESSION ['_INSTALL_DB_DATA'] ['engine'] ? $_SESSION ['_INSTALL_DB_DATA'] ['engine'] : 'InnoDB';
                }
                $sql = $builder->schema ( $dao, $engine );
            }
            if ($sql) {
                $rst = $driver->exec ( $sql );
                if ($rst !== false) {
                    return true;
                }
            } else {
                db_error ( 'Cannot create SQL for creating table' );
            }
            return false;
        } catch ( PDOException $e ) {
            db_error ( $e->getMessage () . ($sql ? $sql : '') );
            return false;
        }
    }
    /**
     * 
     * safe field and their parameter name
     * @param string $field
     * @param string $char
     * @return array
     */
    public static function safeField($field, $char = '`') {
        if ($field instanceof DbImmutableF) {
            $field->setSpecialChar ( $char );
            $sf = $field->__toString ();
        } else {
            $sf = "{$char}{$field}{$char}";
            $sfs = explode ( '.', $field );
            if (count ( $sfs ) > 1) {
                $sf = "{$char}{$sfs [0]}{$char}.{$char}{$sfs [1]}{$char}";
            }
        }
        $name = str_replace ( array ($char, '.', '(', ')' ), array ('', '_', '_', '_' ), $sf );
        return array ($sf, $name );
    }
    public static function compareField($field1, $field2) {}
    /**
     * 
     * 
     * @return SqlBuilder
     */
    public abstract function getSqlBuilder();
}