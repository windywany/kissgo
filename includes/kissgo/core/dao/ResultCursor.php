<?php
class ResultCursor implements Countable, IteratorAggregate, ArrayAccess {
    protected $condition = array ();
    protected $joins = array ();
    protected $fields = array ();
    protected $limit = array ();
    protected $order = array ();
    protected $group = array ();
    protected $having = array ();
    /**
     * @var Idao
     */
    protected $dao = null;
    /**
     * @var PdoDriver
     */
    protected $driver = null;
    /**  
     * @var SqlBuilder
     */
    protected $builder = null;
    /**
     * @var PDOStatement
     */
    protected $stmt = null;
    protected $total = - 1;
    /**
     * @var DbSQL
     */
    protected $sql = null;
    protected $alias = null;
    protected $errorInfo = null;
    /**
     * 
     * create a Cursor for fetch result from database
     * @param Idao $dao
     * @param array $fields
     */
    public function __construct($dao, $fields, $alias) {
        $this->dao = $dao;
        $this->driver = $dao->getDriver ();
        $this->alias = $alias;
        $this->builder = $this->driver->getSqlBuilder ();
        if (! is_array ( $fields )) {
            $this->fields = explode ( ",", $fields );
        } else {
            $this->fields = $fields;
        }
    }
    public function __destruct() {
        if ($this->stmt) {
            $this->stmt->closeCursor ();
        }
        if ($this->countStmt) {
            $this->countStmt->closeCursor ();
        }
    }
    public function __get($name) {
        if ($name == '__PdoStatement') {
            $this->errorInfo = false;
            if ($this->stmt == null) {
                $sql = $this->builder->select ( array ($this->dao, $this->alias ), $this->fields, $this->joins, $this->condition, $this->group, $this->order, $this->having, $this->limit );
                if ($sql) {
                    try {
                        $this->stmt = $sql->query ( $this->driver );
                        $this->stmt->setFetchMode ( PDO::FETCH_ASSOC );
                    } catch ( Exception $e ) {
                        $this->errorInfo = $this->driver->errorInfo ();
                        log_debug ( $e->getMessage () );
                        throw $e;
                    }
                }
            }
            if ($this->stmt) {
                $this->__PdoStatement = $this->stmt;
                return $this->stmt;
            }
            throw new Exception ( "the PdoStatment is null", 500, NULL );
        }
        return null;
    }
    public function lastErrorMsg() {
        if ($this->errorInfo) {
            return $this->errorInfo [2];
        }
        return false;
    }
    /**
     * 
     * build where
     * @param array|mixed $condition a set of condition or a value of primary key of the main table
     * @return ResultCursor
     */
    public function where($condition) {
        if (is_array ( $condition )) {
            $this->condition += $condition;
        }
        return $this;
    }
    /**
     * 
     * left join
     * @param Idao $table
     * @param String $on
     * @param string|null $alias
     * @return ResultCursor
     */
    public function ljoin($table, $on, $alias = null) {
        $join = array ($table, $on, ' LEFT JOIN ', $alias == null ? $table : $alias );
        $this->joins [] = $join;
        return $this;
    }
    /**
     * 
     * right join
     * @param Ido $table
     * @param string  $on
     * @param string|null $alias
     * @return ResultCursor
     */
    public function rjoin($table, $on, $alias = null) {
        $join = array ($table, $on, ' RIGHT JOIN ', $alias == null ? $table : $alias );
        $this->joins [] = $join;
        return $this;
    }
    /**
     * 
     * inner join
     * @param Idao $table
     * @param string $on
     * @param string|null $alias
     * @return ResultCursor
     */
    public function ijoin($table, $on, $alias = null) {
        $join = array ($table, $on, ' INNER JOIN ', $alias == null ? $table : $alias );
        $this->joins [] = $join;
        return $this;
    }
    /**
     * 
     * pagination 
     * @param int $start start page, start from 1
     * @param int $limit items per page
     * @return ResultCursor
     */
    public function limit($start = 1, $limit = 10) {
        $start = intval ( $start );
        if ($start == 0) {
            $start = 1;
        }
        $limit = intval ( $limit );
        if ($limit == 0) {
            $limit = 10;
        }
        $this->limit = array ($start, $limit );
        return $this;
    }
    /**
     * 
     * order the result
     * @param string $field
     * @param string $dir
     * @return ResultCursor
     */
    public function sort($field, $dir = "DES") {
        if (! empty ( $field )) {
            $this->order [] = array ($field, $dir == 'ASC' ? 'ASC' : 'DES' );
        }
        return $this;
    }
    /**
     * 
     * group by
     * @param string $groupby
     * @return ResultCursor
     */
    public function groupby($groupby) {
        if (! empty ( $groupby )) {
            $this->group [] = $groupby;
        }
        return $this;
    }
    /**
     * 
     * having
     * @param string $having
     * @return ResultCursor
     */
    public function having($having) {
        if (! empty ( $having )) {
            $this->having [] = $having;
        }
        return $this;
    }
    /**
     * 
     * add one or more fields
     * @param string $field...
     * @return ResultCursor
     */
    public function field($field, $alias = null) {
        if (! empty ( $field )) {
            $this->fields [] = $field . ($alias == null ? '' : ' AS ' . $alias);
        }
        return $this;
    }
    /**
     * 
     * return the size of the result
     */
    public function size() {
        try {
            return $this->__PdoStatement->rowCount ();
        } catch ( Exception $e ) {
            return 0;
        }
    }
    /**
     * return the count total
     * @see Countable::count()
     * @return int
     */
    public function count($field = null) {
        if ($this->total < 0) {
            $field = $field ? imtf ( $field, 'total' ) : imtf ( 'COUNT(*)', 'total' );
            $sql = $this->builder->select ( array ($this->dao, $this->alias ), array ($field ), $this->joins, $this->condition, $this->group, null, $this->having, null );
            if ($sql != null) {
                try {
                    $this->errorInfo = false;
                    $rst = $sql->query ( $this->driver );
                    $item = $rst->fetch ( PDO::FETCH_ASSOC );
                    $this->total = $item ['total'];
                } catch ( PDOException $e ) {
                    $this->errorInfo = $this->driver->errorInfo ();
                    log_debug ( $e->getMessage () );
                }
            } else {
                return false;
            }
        }
        return $this->total;
    }
    
    /* (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator() {
        try {
            rewind ( $this->__PdoStatement );
            return $this->__PdoStatement;
        } catch ( Exception $e ) {
            return new ArrayIterator ( array () );
        }
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        if (! is_int ( $offset ) || $offset < 1) {
            return false;
        }
        try {
            rewind ( $this->__PdoStatement );
            $rst = $this->__PdoStatement->fetch ( PDO::FETCH_CLASS, PDO::FETCH_ORI_NEXT, $offset );
            if ($rst) {
                return true;
            }
            return false;
        } catch ( Exception $e ) {
            return false;
        }
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        if (! is_int ( $offset ) || $offset < 0) {
            return array ();
        }
        try {
            rewind ( $this->__PdoStatement );
            $rst = $this->__PdoStatement->fetch ( PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT, $offset );
            if ($rst) {
                return $rst;
            }
            return array ();
        } catch ( Exception $e ) {
            return array ();
        }
    }
    
    public function offsetSet($offset, $value) {}
    
    public function offsetUnset($offset) {}
}