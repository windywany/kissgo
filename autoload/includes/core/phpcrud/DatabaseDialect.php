<?php
/**
 * 
 * deal with the difference between various databases
 * @author guangfeng.ning
 *
 */
abstract class DatabaseDialect extends PDO {
    private static $INSTANCE = array ();
    private $tablePrefix;
    public static $lastErrorMassge = '';
    public function __construct($options) {
        list ( $dsn, $user, $passwd, $attr ) = $this->prepareConstructOption ( $options );
        if (! isset ( $attr [PDO::ATTR_EMULATE_PREPARES] )) {
            $attr [PDO::ATTR_EMULATE_PREPARES] = false;
        }
        parent::__construct ( $dsn, $user, $passwd, $attr );
        $this->tablePrefix = isset ( $options ['prefix'] ) && ! empty ( $options ['prefix'] ) ? $options ['prefix'] . '_' : '';
    }
    /**
     * get the database dialect by the $name
     * 
     * @param string $name
     * @return DatabaseDialect
     */
    public final static function getDialect($name = null) {
        try {
            $name = $name ? $name : 'default';
            self::$lastErrorMassge = false;
            if (! isset ( self::$INSTANCE [$name] )) {
                $settings = KissGoSetting::getSetting ();
                if (! isset ( $settings ['database'] )) {
                    trigger_error ( 'the configuration for database is not found!', E_USER_ERROR );
                }
                $database_settings = $settings ['database'];
                if (! isset ( $database_settings [$name] )) {
                    trigger_error ( 'the configuration for database: ' . $name . ' is not found!', E_USER_ERROR );
                }
                $options = $database_settings [$name];
                $driver = isset ( $options ['driver'] ) && ! empty ( $options ['driver'] ) ? $options ['driver'] : 'MySQL';
                $driverClz = $driver . 'Dialect';
                if (! is_subclass_of2 ( $driverClz, 'DatabaseDialect' )) {
                    trigger_error ( 'the dialect ' . $driverClz . ' is not found!', E_USER_ERROR );
                }
                $dr = new $driverClz ( $options );
                $dr->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                self::$INSTANCE [$name] = $dr;
            }
            return self::$INSTANCE [$name];
        } catch ( PDOException $e ) {
            self::$lastErrorMassge = $e->getMessage ();
            return null;
        }
    }
    /**
     * get the full table name( prepend the prefix to the $table)
     * 
     * @param string $table
     * @return string
     */
    public function getTableName($table) {
        if (preg_match ( '#^\{[^\}]+\}.*$#', $table )) {
            return str_replace ( array ('{', '}' ), array ($this->tablePrefix, '' ), $table );
        } else {
            return $table;
        }
    }
    /**
     * get a select SQL for retreiving data from database.
     * 
     * @param array $fields
     * @param array $from
     * @param array $joins
     * @param Condition $where
     * @param array $having
     * @param array $group
     * @param array $order
     * @param array $limit
     * @param BindValues $values
     * @return string
     */
    public abstract function getSelectSQL($fields, $from, $joins, $where, $having, $group, $order, $limit, $values);
    /**
     * get a select sql for geting the count from database
     * 
     * @param array $fields
     * @param array $from
     * @param array $joins
     * @param Condition $where
     * @param array $having
     * @param array $group
     * @param BindValues $values
     * @return string
     */
    public abstract function getCountSelectSQL($field, $from, $joins, $where, $having, $group, $values);
    /**
     * get the insert SQL
     * 
     * @param string $into
     * @param array $data
     * @param BindValues $values
     * @return string
     */
    public abstract function getInsertSQL($into, $data, $values);
    /**
     * get the update SQL
     * 
     * @param array $table
     * @param array $data
     * @param Condition $where
     * @param BindValues $values
     * @return string
     */
    public abstract function getUpdateSQL($table, $data, $where, $values);
    /**
     * get the delete SQL
     * 
     * @param string $from
     * @param array $using
     * @param Condition $where
     * @param BindValues $values
     * @return string
     */
    public abstract function getDeleteSQL($from, $using, $where, $values);
    /**
     * transfer the char ` to a proper char
     * 
     * @param string $string
     * @return string
     */
    public abstract function sanitize($string);
    /**
     * prepare the construct option, the return must be an array, detail listed following:
     * 1. dsn
     * 2. username
     * 3. password
     * 4. attributes 
     * @param array $options
     * @return array  array ( dsn, user,passwd, attr ) 
     */
    protected abstract function prepareConstructOption($options);
}
