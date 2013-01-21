<?php
/**
 * 
 * database driver base class
 * @author guangfeng.ning
 *
 */
abstract class PdoDriver extends PDO {
    protected $tbl_prefix = '';
    public function __construct($options) {
        list ( $dsn, $user, $passwd, $attr ) = $this->buildOptions ( $options );
        parent::__construct ( $dsn, $user, $passwd, $attr );
    }
    public function getFullTableName($name) {
        return $this->tbl_prefix . $name;
    }
    public static function getDriver($database = 'default') {
        $driver = 'mysql';
        include_once dirname ( __FILE__ ) . '/mysql/MysqlPdoDriver.php';
        $driverIns = new MysqlPdoDriver ( array ('host' => 'localhost', '' ) );
        $driverIns->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        return $driverIns;
    }
    /**
     * 
     * create table
     * @param Idao $dao
     * @param string $database
     * @return boolean
     */
    public static function createTable($dao, $database = 'default') {
        try {
            $driver = PdoDriver::getDriver ( $database );
            $sql = $dao->getCreateSql ();
            if (empty ( $sql )) {
                $fields = array ();
                $builder = $driver->getSqlBuilder ();
                $sql = $builder->schema ( $dao );
            }
            if ($sql) {
                $rst = $driver->exec ( $sql );
                if ($rst) {
                    return true;
                }
            }
            return false;
        } catch ( PDOException $e ) {
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
    public static function compareField($field1, $field2) {
        
    }
    /**
     * 
     * 
     * @return SqlBuilder
     */
    public abstract function getSqlBuilder();
}