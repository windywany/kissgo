<?php
/**
 * 查询结果集
 * @author Leo Ning
 *
 */
class ResultCursor extends DbSqlHelper implements Countable, IteratorAggregate, ArrayAccess {
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
        if ($fields instanceof DbImmutableF) {
            $this->fields = array ($fields );
        } else if (! is_array ( $fields )) {
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
        if ($name == 'rows') {
            $this->errorInfo = false;
            if ($this->stmt == null) {
                $sql = $this->builder->select ( array ($this->dao, $this->alias ), $this );
                if ($sql) {
                    $this->stmt = $sql->query ( $this->driver );
                }
            }
            if ($this->stmt) {
                $this->stmt->setFetchMode ( PDO::FETCH_ASSOC );
                $this->params = $sql->values ();
                $this->rows = $this->stmt->fetchAll ();
                return $this->rows;
            }
            db_error ( "the PdoStatment is null" );
        }
        return array();
    }
    public function lastErrorMsg() {
        if ($this->errorInfo) {
            return $this->errorInfo [2];
        }
        return false;
    }
    /**
     * 
     * return the size of the result
     */
    public function size() {
        return count ( $this->rows );
    }
    /**
     * 返回结果集的数组
     * @param string/int $field 使用指定字段值做为数组的KEY
     * @param string/int $vfield 使用指定字段值做为数组的值，如果为空则使用整条记录做为值
     * @return array
     */
    public function toArray($field = "", $vfield = "", $results = array()) {
        if (! empty ( $field ) || is_numeric ( $field )) {
            foreach ( $this->rows as $key => $value ) {
                $key = $value [$field];
                $value = empty ( $vfield ) ? $value : $value [$vfield];
                $results [$key] = $value;
            }
        } else {
            $results += $this->rows;
        }
        return $results;
    }
    public function walk($callback) {
        $results = array ();
        foreach ( $this->rows as $key => $value ) {
            $walked = call_user_func_array ( $callback, array ($key, $value ) );
            if (count ( $walked ) == 2) {
                list ( $keyx, $value ) = $walked;
                $results [$keyx] = $value;
            }
        }
        return $results;
    }
    /**
     * 列转行
     * 
     * 将$field对应的列转为一行
     * 
     * @param string $field 结果集中的一列字段
     * @return array
     */
    public function r2c($field) {
        $results = array ();
        foreach ( $this->rows as $key => $value ) {
            if (isset ( $value [$field] )) {
                $results [] = $value [$field];
            }
        }
        return $results;
    }
    /**
     * return the count total
     * @see Countable::count()
     * @return int
     */
    public function count($field = null) {
        if ($this->total < 0) {
            $this->total = 0;
            $field = $field ? imtf ( $field, 'total' ) : imtf ( 'COUNT(*)', 'total' );
            if (! $this->hasHavingField ()) {
                $field = $field ? imtf ( $field, 'total' ) : imtf ( 'COUNT(*)', 'total' );
                $sql = $this->builder->select ( array ($this->dao, $this->alias ), $this->getTotalHelper ( $field ) );
            } else {
                $field = imtf ( 'COUNT(*)', 'total' );
                $sql1 = $this->builder->select ( array ($this->dao, $this->alias ), $this->getTotalHelper () );
                $helper = new DbSqlHelper ();
                $helper->field ( $field );
                $sql = $this->builder->select ( array ($sql1, 'TMP_CNT_TABLE' ), $helper );
            }
            if ($sql != null) {
                $this->errorInfo = false;
                $rst = $sql->query ( $this->driver );
                if ($rst) {
                    $item = $rst->fetch ( PDO::FETCH_ASSOC );
                    $this->total = $item ['total'];
                }
            }
        }
        return $this->total;
    }
    public function getIterator() {
        try {
            return new ArrayIterator ( $this->rows );
        } catch ( Exception $e ) {
            return new ArrayIterator ( array () );
        }
    }
    public function offsetExists($offset) {
        try {
            if (! is_int ( $offset )) {
                return isset ( $this->rows [0] [$offset] );
            } else {
                return isset ( $this->rows [$offset] );
            }
        } catch ( Exception $e ) {
            return false;
        }
    }
    public function offsetGet($offset) {
        try {
            if (! is_int ( $offset )) {
                return $this->rows [0] [$offset];
            } else {
                return $this->rows [$offset];
            }
        } catch ( Exception $e ) {
            echo $e->getMessage ();
            return array ();
        }
    }
    public function hasHavingField() {
        foreach ( $this->fields as $key => $f ) {
            if (! is_numeric ( $key )) {
                return true;
            }
        }
        return false;
    }
    public function __toString() {
        $str = '';
        if (! $this->stmt) {
            $sql = $this->builder->select ( array ($this->dao, $this->alias ), $this );
            if ($sql) {
                $this->params += $sql->values ();
                $str = $sql->__toString ();
            }
        } else {
            $str = $this->stmt->queryString;
        }
        return $str;
    }
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
}