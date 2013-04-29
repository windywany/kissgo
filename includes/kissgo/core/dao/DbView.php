<?php
/**
 * 
 * 数据库中视图
 * @author Leo Ning
 *
 */
abstract class DbView implements Idao {
    protected $alias = '';
    /**     
     * @var PdoDriver
     */
    protected $driver = null;
    /**     
     * @var SqlBuilder
     */
    protected $builder = null;
    protected $specialChar = '';
    public function __construct($database = 'default') {
        try {
            $this->driver = PdoDriver::getDriver ( $database );
        } catch ( PDOException $e ) {
            trigger_error ( $e->getMessage (), E_USER_ERROR );
        }
        $this->alias = preg_replace ( '/(Table|View)$/', '', get_class ( $this ) );
        if (! isset ( $this->table ) || empty ( $this->table )) {
            $this->table = strtolower ( $this->alias );
        }
        $this->builder = $this->driver->getSqlBuilder ();
        $this->specialChar = $this->builder->specialChar ();
    }
    public function getAlias() {
        return $this->alias;
    }
    /**
     * (non-PHPdoc)
     * @see Idao::delete()
     */
    public function delete($alias = null) {
        return false;
    }
    /**
     * (non-PHPdoc)
     * @see Idao::getDriver()
     * @return PdoDriver
     */
    public function getDriver() {
        return $this->driver;
    }
    /**
     * (non-PHPdoc)
     * @see Idao::lastId()
     */
    public function lastId($name = null) {
        return - 1;
    }
    /**
     * (non-PHPdoc)
     * @see Idao::getFullTableName()
     */
    public function getFullTableName() {
        static $fullname = false;
        if (! $fullname) {
            $fullname = $this->driver->getFullTableName ( $this->table );
        }
        return $fullname;
    }
    /**
     * (non-PHPdoc)
     * @see Idao::query()
     * @return ResultCursor
     */
    public function query($fields = '*', $alias = null) {
        $alias = $alias == null ? $this->alias : $alias;
        return new ResultCursor ( $this, $fields, $alias );
    }
    public function read($where) {
        $rst = new ResultCursor ( $this, "*", $this->alias );
        $rst->where ( $where )->limit ( 1, 1 );
        return $rst [0];
    }
    /**
     * 
     * @param unknown_type $data
     * @return boolean
     */
    public function exist($data) {
        try {
            $rst = $this->query ( '*' )->where ( $data );
            return count ( $rst ) > 0;
        } catch ( PDOException $e ) {
            return false;
        }
    }
    /**
     * (non-PHPdoc)
     * @see Idao::save()
     */
    public function save($data, $alias = null) {
        return false;
    }
    public function insert($data) {
        return false;
    }
    public function remove($where) {
        return false;
    }
    public function update($data, $where) {
        return false;
    }
    
    /* (non-PHPdoc)
     * @see Idao::schema()
     * @return DbSchema
     */
    public function schema() {
        return new DbSchema ();
    }
    public function getCreateSql() {
        return false;
    }
    /* (non-PHPdoc)
     * @see Idao::getPrepareFields()
     */
    public static function prepareFields($fields, &$values, $specialChar, $dao = null) {
        $_fields = array ();
        foreach ( $fields as $key => $field ) {
            if ($field == '*') {
                $_fields [] = '*';
                break;
            } else if ($field instanceof DbImmutableF) {
                $field->setSpecialChar ( $specialChar );
                $_fields [] = $field->__toString ();
            } else if ($field instanceof ResultCursor) {
                $sql = $field->__toString ();
                if ($sql && is_string ( $key )) {
                    $_fields [] = '(' . $sql . ') AS ' . $specialChar . $key . $specialChar;
                    $params = $field->getParams ();
                    if (! empty ( $params )) {
                        $values->merge ( $params );
                    }
                }
            } else {
                $_fd = preg_split ( '#\b(as|\s+)\b#i', trim ( $field ) );
                $alias = '';
                if (count ( $_fd ) > 1) {
                    $alias = ' AS ' . $specialChar . array_pop ( $_fd ) . $specialChar;
                }
                $_fd = explode ( ".", $_fd [0] );
                $table = '';
                if (count ( $_fd ) > 1) {
                    $table = $specialChar . trim ( $_fd [0] ) . $specialChar . ".";
                    $field = trim ( $_fd [1] );
                } else {
                    $field = trim ( $_fd [0] );
                }
                if ($field != '*') {
                    $_fields [] = $table . $specialChar . $field . $specialChar . $alias;
                } else {
                    $_fields [] = $table . $field . $alias;
                }
            }
        }
        if (! empty ( $_fields )) {
            return implode ( ",", $_fields );
        }
        return false;
    }
}