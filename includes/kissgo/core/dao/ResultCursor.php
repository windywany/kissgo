<?php
class ResultCursor implements Countable {
    protected $condition = array ();
    protected $joins = array ();
    protected $fields = array ();
    protected $limit = array ();
    protected $order = array ();
    protected $group = array ();
    protected $having = array ();
    
    protected $dao = null;
    protected $driver = null;
    /**     
     * @var SqlBuilder
     */
    protected $builder = null;
    protected $stmt = null;
    protected $countStmt = null;
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
     */
    public function getSelectSql() {
        // todo filter the optinos
        return $this->builder->select ( array ($this->dao->getFullTableName (), $this->alias ), $this->fields, $this->joins, $this->condition, $this->group, $this->order, $this->having, $this->limit );
    }
    /**
     * 
     * return the size of the result
     */
    public function size() {
        $this->getSelectSql ();
    }
    /**
     * return the count total
     * @see Countable::count()
     */
    public function count() {
        // TODO Auto-generated method stub        
    }
}