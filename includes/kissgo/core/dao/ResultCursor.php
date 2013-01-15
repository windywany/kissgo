<?php
class ResultCursor implements Countable, Iterator, ArrayAccess {
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
     * get the sql depends on this Cursor
     * @return DbSQL
     */
    public function getSelectSql() {
        //TODO filter the optinos
        $sql = $this->builder->select ( array ($this->dao->getFullTableName (), $this->alias ), $this->fields, $this->joins, $this->condition, $this->group, $this->order, $this->having, $this->limit );
        return $sql;
    }
    /**
     * 
     * return the size of the result
     */
    public function size() {
        if (! $this->stmt) {
            $this->execute ();
        }
        if ($this->stmt) {
            return $this->stmt->rowCount ();
        } else {
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
            $sql = $this->builder->select ( array ($this->dao->getFullTableName (), $this->alias ), array ($field ), $this->joins, $this->condition, $this->group, null, $this->having, null );
            try {
                $rst = $sql->query ( $this->driver );
                $item = $rst->fetch ( PDO::FETCH_ASSOC );
                $this->total = $item ['total'];
            } catch ( PDOException $e ) {
                log_error ( $e->getMessage () );
            }
        }
        return $this->total;
    }
    protected function execute() {
        try {
            $sql = $this->getSelectSql ();
            $this->stmt = $sql->query ( $this->driver );
        } catch ( PDOException $e ) {
            log_error ( $e->getMessage () );
        }
    }
    /* (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current() {
        // TODO Auto-generated method stub
    }
    
    /* (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key() {
        // TODO Auto-generated method stub
    }
    
    /* (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next() {
        // TODO Auto-generated method stub
    }
    
    /* (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind() {
        // TODO Auto-generated method stub
    }
    
    /* (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid() {
        // TODO Auto-generated method stub
    }
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        // TODO Auto-generated method stub
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        // TODO Auto-generated method stub
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        // TODO Auto-generated method stub
    }
    
    /* (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        // TODO Auto-generated method stub
    }
}