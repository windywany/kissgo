<?php
class Query extends QueryBuilder implements Countable, ArrayAccess, IteratorAggregate {
    private $fields = array ();
    private $performed = false;
    private $countperformed = false;
    private $size = 0;
    private $count = 0;
    private $resultSet = array ();
    private $statement;
    private $countStatement;
    
    public function __construct() {
        parent::__construct ();
        $args = func_get_args ();
        if ($args) {
            foreach ( $args as $a ) {
                if (is_array ( $a )) {
                    foreach ( $a as $f ) {
                        $this->field ( $f );
                    }
                } else {
                    $this->field ( $a );
                }
            }
        }
    }
    /**
     * append a field to result set. 
     * 
     * @param string|Query $field
     * @param string $alias
     * @return QueryBuilder
     */
    public function field($field, $alias = null) {
        if (is_string ( $field )) {
            $this->fields [] = $field . ($alias ? ' AS ' . $alias : '');
        } else if ($field instanceof Query) {
            if ($alias) {
                $field->alias ( $alias );
            }
            $this->fields [] = $field;
        }
        return $this;
    }
    /**
     * check if there is any row in database suits the condition.
     * 
     * @return boolean
     */
    public function exist() {
        return $this->count () > 0;
    }
    /**
     * 1. The implementation of Countable interface, so, you can count this class instance directly to get the size of the result set.<br/>
     * 2. Specify the $field argument to perform a 'select count($field)' operation, if the SQL has a having sub-sql, please note that the $field variables must contain the fields.
     * 
     * @see Countable::count()
     * @return the number of result set or the count total or false on error SQL.
     */
    public function count($field = null) {
        if ($field == null) {
            if (! $this->performed) {
                $this->perform ();
            }
            return $this->size;
        } else {
            if (! $this->countperformed) {
                call_user_func_array ( array ($this, 'performCount' ), func_get_args () );
            }
            return $this->count;
        }
        return false;
    }
    
    public function offsetExists($offset) {
        if (! $this->performed) {
            $this->perform ();
        }
        return isset ( $this->resultSet [$offset] );
    }
    
    public function offsetGet($offset) {
        if (! $this->performed) {
            $this->perform ();
        }
        if (isset ( $this->resultSet [$offset] )) {
            return $this->resultSet [$offset];
        }
        return null;
    }
    
    public function offsetSet($offset, $value) {}
    
    public function offsetUnset($offset) {}
    
    public function getIterator() {
        if (! $this->performed) {
            $this->perform ();
        }
        return new ArrayIterator ( $this->resultSet );
    }
    
    public function __toString() {
        $sql = $this->getSQL ();
        return $sql;
    }
    /**
     * perform the select statement
     * 
     */
    private function perform() {
        $sql = $this->getSQL ();
        if ($sql) {
            try {
                $this->options [PDO::ATTR_CURSOR] = PDO::CURSOR_SCROLL;
                $this->statement = $this->dialect->prepare ( $sql, $this->options );
                if ($this->values) {
                    foreach ( $this->values as $value ) {
                        list ( $name, $val, $type ) = $value;
                        if (! $this->statement->bindValue ( $name, $val, $type )) {
                            $this->performed = true;
                            $this->size = false;
                            $this->errorSQL = $sql;
                            $this->errorValues = $this->values->__toString ();
                            $this->error = 'can not bind the value ' . $val . '[' . $type . '] to the argument:' . $name;
                            return;
                        }
                    }
                }
                $rst = $this->statement->execute ();
                if ($rst) {
                    $this->resultSet = $this->statement->fetchAll ( PDO::FETCH_ASSOC );
                    $this->size = count ( $this->resultSet );
                }
            } catch ( PDOException $e ) {
                $this->error = $e->getMessage ();
                $this->size = false;
                $this->errorSQL = $sql;
                $this->errorValues = $this->values->__toString ();
            }
        } else {
            $this->size = false;
            $this->error = 'can not generate the SQL';
            $this->errorSQL = '';
            $this->errorValues = $this->values->__toString ();
        }
        $this->performed = true;
    }
    /**
     * perform the select count($field) statement.
     * 
     * @param string $field
     */
    private function performCount() {
        $this->checkDialect ();
        $values = new BindValues ();
        $fields = func_get_args ();
        $fields [0] = 'COUNT(' . $fields [0] . ')';
        $fields = $this->prepareFields ( $fields, $values );
        $from = $this->prepareFrom ( $this->sanitize ( $this->from ) );
        $joins = $this->prepareJoins ( $this->sanitize ( $this->joins ) );
        $having = $this->sanitize ( $this->having );
        $group = $this->sanitize ( $this->group );
        $sql = $this->dialect->getCountSelectSQL ( $fields, $from, $joins, $this->where, $having, $group, $values );
        if ($sql) {
            try {
                $this->options [PDO::ATTR_CURSOR] = PDO::CURSOR_SCROLL;
                $statement = $this->dialect->prepare ( $sql, $this->options );
                if ($values) {
                    foreach ( $values as $value ) {
                        list ( $name, $val, $type ) = $value;
                        if (! $statement->bindValue ( $name, $val, $type )) {
                            $this->countperformed = true;
                            $this->count = false;
                            $this->errorSQL = $sql;
                            $this->errorValues = $values->__toString ();
                            $this->error = 'can not bind the value ' . $val . '[' . $type . '] to the argument:' . $name;
                            return;
                        }
                    }
                }
                $rst = $statement->execute ();
                if ($rst) {
                    $resultSet = $statement->fetch ( PDO::FETCH_NUM );
                    $this->count = $resultSet [0];
                }
            } catch ( PDOException $e ) {
                $this->error = $e->getMessage ();
                $this->count = false;
                $this->errorSQL = $sql;
                $this->errorValues = $values->__toString ();
            }
        } else {
            $this->count = false;
            $this->errorSQL = '';
            $this->errorValues = $values->__toString ();
            $this->error = 'can not generate the SQL';
        }
        $this->countperformed = true;
    }
    /**
     * 
     * get the raw SQL
     */
    private function getSQL() {
        $this->checkDialect ();
        if (! $this->values) {
            $this->values = new BindValues ();
        }
        $fields = $this->prepareFields ( $this->fields, $this->values );
        $from = $this->prepareFrom ( $this->sanitize ( $this->from ) );
        $joins = $this->prepareJoins ( $this->sanitize ( $this->joins ) );
        $having = $this->sanitize ( $this->having );
        $group = $this->sanitize ( $this->group );
        $order = $this->sanitize ( $this->order );
        return $this->dialect->getSelectSQL ( $fields, $from, $joins, $this->where, $having, $group, $order, $this->limit, $this->values );
    }
}
