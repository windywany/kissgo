<?php
/**
 * for postgres SQL
 * 
 * @author guangfeng.ning
 *
 */
class PostgreSQLDialect extends DatabaseDialect {
    /**
     * (non-PHPdoc)
     * @see DatabaseDialect::getSelectSQL()
     * @param Query $query
     */
    public function getSelectSQL($fields, $from, $joins, $where, $having, $group, $order, $limit, $values) {
        $sql = array ('SELECT', $fields, 'FROM' );
        $this->generateSQL ( $sql, $from, $joins, $where, $having, $group, $values );
        if ($order) {
            $_orders = array ();
            foreach ( $order as $o ) {
                $_orders [] = $o [0] . ' ' . $o [1];
            }
            $sql [] = 'ORDER BY ' . implode ( ' , ', $_orders );
        }
        if ($limit) {
            $limit1 = $values->addValue ( 'limit', $limit [0] );
            $limit2 = $values->addValue ( 'limit', $limit [1] );
            $sql [] = 'LIMIT ' . $limit2 . ' OFFSET ' . $limit1;
        }
        $sql = implode ( ' ', $sql );
        return $sql;
    }
    /**
     * (non-PHPdoc)
     * @see DatabaseDialect::getCountSelectSQL()
     */
    public function getCountSelectSQL($fields, $from, $joins, $where, $having, $group, $values) {
        $sql = array ('SELECT', $fields, 'FROM' );
        $this->generateSQL ( $sql, $from, $joins, $where, $having, $group, $values );
        $sql = implode ( ' ', $sql );
        return $sql;
    }
    /**
     * (non-PHPdoc)
     * @see DatabaseDialect::getInsertSQL()
     */
    public function getInsertSQL($into, $data, $values) {
        $sql = "INSERT INTO $into (\"";
        $fields = $_values = array ();
        foreach ( $data as $field => $value ) {
            $fields [] = $field;
            if ($value instanceof ImmutableValue) { // a immutable value
                $_values [] = $this->sanitize ( $value->__toString () );
            } else if ($value instanceof Query) { // a sub-select SQL as a value
                $value->setBindValues ( $values );
                $value->setDialect ( $this );
                $_values [] = '(' . $value->__toString () . ')';
            } else {
                $_values [] = $values->addValue ( $field, $value );
            }
        }
        $sql .= implode ( '" , "', $fields ) . '") VALUES (' . implode ( ' , ', $_values ) . ')';
        return $sql;
    }
    /**
     * (non-PHPdoc)
     * @see DatabaseDialect::getDeleteSQL()
     */
    public function getDeleteSQL($from, $using, $where, $values) {
        $sql [] = 'DELETE FROM ' . $from [0];
        if ($using) {
            $us = array ();
            foreach ( $using as $u ) {
                $us [] = $u [0];
            }
            $sql [] = 'USING';
            $sql [] = implode ( ' , ', $us );
        }
        if ($where) {
            $sql [] = 'WHERE';
            $sql [] = $where->getWhereCondition ( $this, $values );
        }
        return implode ( ' ', $sql );
    }
    /**
     * (non-PHPdoc)
     * @see DatabaseDialect::getUpdateSQL()
     */
    public function getUpdateSQL($table, $data, $where, $values) {
        $sql = array ('UPDATE', $table, 'SET' );
        $fields = array ();
        foreach ( $data as $field => $value ) {
            if ($value instanceof Query) {
                $value->setBindValues ( $values );
                $value->setDialect ( $this );
                $fields [] = $this->sanitize ( $field ) . ' =  (' . $value->__toString () . ')';
            } else if ($value instanceof ImmutableValue) {
                $fields [] = $this->sanitize ( $field ) . ' =  ' . $this->sanitize ( $value->__toString () );
            } else {
                $fields [] = $this->sanitize ( $field ) . ' = ' . $values->addValue ( $field, $value );
            }
        }
        $sql [] = implode ( ' , ', $fields );
        if ($where) {
            $sql [] = 'WHERE';
            $sql [] = $where->getWhereCondition ( $this, $values );
        }
        return implode ( ' ', $sql );
    }
    /**
     * 
     * @see DatabaseDialect::prepareConstructOption()
     */
    protected function prepareConstructOption($options) {
        return array ('pgsql:dbname=test;host=10.243.118.141;port=5432', 'ngf', '888888', array () );
    }
    
    public function sanitize($string) {
        return str_replace ( '`', '"', $string );
    }
    /**
     * generate the common SQL for select and select count
     * 
     * @param array $sql
     * @param array $from
     * @param array $joins
     * @param Condition $where
     * @param array $having
     * @param array $group
     * @param BindValues $values
     */
    private function generateSQL(&$sql, $from, $joins, $where, $having, $group, $values) {
        $froms = array ();
        foreach ( $from as $f ) {
            $froms [] = $f [0] . ' AS ' . $f [1];
        }
        $sql [] = implode ( ',', $froms );
        if ($joins) {
            foreach ( $joins as $join ) {
                $sql [] = $join [2] . ' ' . $join [0] . ' AS ' . $join [3] . ' ON (' . $join [1] . ')';
            }
        }
        if ($where) {
            $sql [] = 'WHERE ' . $where->getWhereCondition ( $this, $values );
        }
        if ($group) {
            $sql [] = 'GROUP BY ' . implode ( ' , ', $group );
        }
        if ($having) {
            $sql [] = 'HAVING ' . implode ( ' AND ', $having );
        }
    }
}