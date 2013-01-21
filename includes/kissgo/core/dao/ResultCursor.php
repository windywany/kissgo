<?php
/**
 * 结果集
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
                    try {
                        $this->stmt = $sql->query ( $this->driver );
                        $this->stmt->setFetchMode ( PDO::FETCH_ASSOC );
                        $this->params = $sql->values ();
                    } catch ( Exception $e ) {
                        $this->errorInfo = $this->driver->errorInfo ();
                        log_debug ( $e->getMessage () . ' [' . $sql . ']' );
                        throw $e;
                    }
                }
            }
            if ($this->stmt) {
                $this->rows = $this->stmt->fetchAll ();
                return $this->rows;
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
     * return the size of the result
     */
    public function size() {
        try {
            return count ( $this->rows );
        } catch ( Exception $e ) {
            return false;
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
                try {
                    $this->errorInfo = false;
                    $rst = $sql->query ( $this->driver );
                    $item = $rst->fetch ( PDO::FETCH_ASSOC );
                    $this->total = $item ['total'];
                } catch ( PDOException $e ) {
                    $this->errorInfo = $this->driver->errorInfo ();
                    log_debug ( $e->getMessage () . ' [' . $sql . ']' );
                }
            } else {
                return false;
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
            return isset ( $this->rows [$offset] );
        } catch ( Exception $e ) {
            return false;
        }
    }
    public function offsetGet($offset) {
        try {
            return $this->rows [$offset];
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
        if (!$this->stmt) {
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