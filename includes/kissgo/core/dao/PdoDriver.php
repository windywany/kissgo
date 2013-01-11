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
        return new MysqlPdoDriver ( array ('host' => 'localhost', '' ) );
    }
    public static function createSchema($dao, $database = 'default') {

    }
    public abstract function getSqlBuilder();
    public abstract function buildOptions($options);
}