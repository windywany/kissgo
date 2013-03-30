<?php
/**
 * 
 * 帮助类
 * @author Leo Ning
 *
 */
class DbSqlHelper {
    protected $condition = array ();
    protected $joins = array ();
    protected $fields = array ();
    protected $limit = array ();
    protected $order = array ();
    protected $group = array ();
    protected $having = array ();
    protected $params = array ();
    public $errorInfo = '';
    public function getTotalHelper($field = null) {
        $helper = new DbSqlHelper ();
        $helper->condition = $this->condition;
        $helper->joins = $this->joins;
        if ($field) {
            $helper->fields [] = $field;
        }
        foreach ( $this->fields as $key => $f ) {
            if (! is_numeric ( $key )) {
                $helper->fields [$key] = $f;
            }
        }
        $helper->group = $this->group;
        $helper->having = $this->having;
        $helper->params = $this->params;
        return $helper;
    }
    
    /**
     * 
     * build where
     * @param array|mixed $condition a set of condition or a value of primary key of the main table
     * @return DbSqlHelper
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
     * @return DbSqlHelper
     */
    public function ljoin($table, $on, $alias = null) {
        if ($alias == null && is_string ( $table ) && preg_match ( '#(.+)\s+AS\s+(.+)#i', $table, $m )) {
            $table = $m [1];
            $alias = $m [2];
        }
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
     * @return DbSqlHelper
     */
    public function rjoin($table, $on, $alias = null) {
        if ($alias == null && is_string ( $table ) && preg_match ( '#(.+)\s+AS\s+(.+)#i', $table, $m )) {
            $table = $m [1];
            $alias = $m [2];
        }
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
     * @return DbSqlHelper
     */
    public function ijoin($table, $on, $alias = null) {
        if ($alias == null && is_string ( $table ) && preg_match ( '#(.+)\s+AS\s+(.+)#i', $table, $m )) {
            $table = $m [1];
            $alias = $m [2];
        }
        $join = array ($table, $on, ' INNER JOIN ', $alias == null ? $table : $alias );
        $this->joins [] = $join;
        return $this;
    }
    /**
     * 
     * pagination 
     * @param int $start start page, start from 1
     * @param int $limit items per page
     * @return DbSqlHelper
     */
    public function limit($start = 1, $limit = 1) {
        $start = intval ( $start );
        if ($start == 0) {
            $start = 1;
        }
        $limit = intval ( $limit );
        if ($limit == 0) {
            $limit = 1;
        }
        $this->limit = array ($start, $limit );
        return $this;
    }
    /**
     * 
     * order the result
     * @param string $field
     * @param string $dir
     * @return DbSqlHelper
     */
    public function sort($field = null, $dir = "d") {
        $fieldx = rqst ( '_sf', false );
        $dir = rqst ( '_sd', $dir );
        if ($fieldx) {
            $this->order [] = array ($fieldx, $dir == 'a' ? 'ASC' : 'DESC' );
        } else if (! empty ( $field )) {
            $this->order [] = array ($field, $dir == 'a' ? 'ASC' : 'DESC' );
        }
        return $this;
    }
    /**
     * 
     * group by
     * @param string $groupby
     * @return DbSqlHelper
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
     * @return DbSqlHelper
     */
    public function having($having) {
        if (! empty ( $having )) {
            $this->having += $having;
        }
        return $this;
    }
    /**
     * 
     * add one field
     * @param string $field
     * @return DbSqlHelper
     */
    public function field($field, $alias = null) {
        if (! empty ( $field )) {
            if ($field instanceof DbImmutableF) {
                $this->fields [] = $field;
            } else if ($field instanceof ResultCursor) {
                $this->fields [$alias] = $field;
            } else {
                $this->fields [] = $field . ($alias == null ? '' : ' AS ' . $alias);
            }
        }
        return $this;
    }
    /**
     * @return the $condition
     */
    public function getCondition() {
        return $this->condition;
    }
    
    /**
     * @return the $joins
     */
    public function getJoins() {
        return $this->joins;
    }
    
    /**
     * @return the $fields
     */
    public function getFields() {
        return $this->fields;
    }
    
    /**
     * @return the $limit
     */
    public function getLimit() {
        return $this->limit;
    }
    
    /**
     * @return the $order
     */
    public function getOrder() {
        return $this->order;
    }
    
    /**
     * @return the $group
     */
    public function getGroup() {
        return $this->group;
    }
    
    /**
     * @return the $having
     */
    public function getHaving() {
        return $this->having;
    }
    public function getParams() {
        return $this->params;
    }
}