<?php
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
        $this->driver = PdoDriver::getDriver ( $database );
        $this->alias = preg_replace ( '/(Table|View)$/', '', get_class ( $this ) );
        if (! isset ( $this->table ) || empty ( $this->table )) {
            $this->table = strtolower ( $this->alias );
        }
        $this->builder = $this->driver->getSqlBuilder ();
        $this->specialChar = $this->builder->specialChar ();
    }
    public function create($data) {
        return false;
    }
    public function delete($condition) {
        return false;
    }
    public function getDriver() {
        return $this->driver;
    }
    public function getFullTableName() {
        static $fullname = false;
        if (! $fullname) {
            $fullname = $this->driver->getFullTableName ( $this->table );
        }
        return $fullname;
    }
    public function query($fields = '*', $alias = null) {
        $alias = $alias == null ? $this->alias : $alias;
        return new ResultCursor ( $this, $fields, $alias );
    }
    public function save($data) {
        return false;
    }
    public function update($data, $condition) {
        return false;
    }
    /* (non-PHPdoc)
     * @see Idao::getPrepareFields()
     */
    public function prepareFields($fields) {
        $_fields = array ();
        foreach ( $fields as $field ) {
            if ($field == '*') {
                $_fields [] = '*';
                break;
            } else if ($field instanceof DbImmutableF) {
                $field->setSpecialChar ( $this->specialChar );
                $_fields [] = $field->__toString ();
            } else {
                $_fd = preg_split ( '#\bas\b#i', $field );
                $alias = '';
                if (count ( $_fd ) > 1) {
                    $alias = ' AS ' . $this->specialChar . trim ( $_fd [1] ) . $this->specialChar;
                }
                $_fd = explode ( ".", $_fd [0] );
                $table = '';
                if (count ( $_fd ) > 1) {
                    $field = trim ( $_fd [1] );
                    $table = $this->specialChar . trim ( $_fd [0] ) . $this->specialChar . ".";
                } else {
                    $field = trim ( $_fd [0] );
                }
                $_fields [] = $table . $this->specialChar . $field . $this->specialChar . $alias;
            }
        }
        if (! empty ( $_fields )) {
            return implode ( ",", $_fields );
        }
        return false;
    }
}