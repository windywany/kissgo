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
    public function __construct($database = 'default') {
        $this->driver = PdoDriver::getDriver ( $database );
        $this->alias = preg_replace ( '/(Table|View)$/', '', get_class ( $this ) );
        if (! isset ( $this->table ) || empty ( $this->table )) {
            $this->table = strtolower ( alias );
        }
        $this->builder = $this->driver->getSqlBuilder ();
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
}