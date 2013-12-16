<?php

/**
 * use this class to build SQL where sub-statement.<br/>
 * simple usage:<br/>
 * $con = new Condition();<br/>
 * <ul>
 * <li> and :  $con['field [op]'] = condition;</li>
 * </ul>
 *
 *
 * @author guangfeng.ning
 *
 */
class Condition implements ArrayAccess {
    private $conditions = array ();

    public function __construct($con = array()) {
        if ($con && is_array ( $con )) {
            foreach ( $con as $key => $value ) {
                $this->conditions [] = array ($key, $value );
            }
        }
    }

    /**
     * get the where sql
     *
     * @param DatabaseDialect $dialect
     * @param BindValues $values
     */
    public function getWhereCondition($dialect, $values) {
        /* || - or
         *  @ - existi
         *  !@ - not exist
         *  $ - null or not null
         */
        $cons = array ();
        foreach ( $this->conditions as $con ) {
            list ( $filed, $value ) = $con;
            if (strpos ( $filed, '||' ) === 0) {
                $cons [] = 'OR';
                $filed = substr ( $filed, 2 );
            } else {
                $cons [] = 'AND';
            }
            $filed = trim ( $filed );
            if ($filed == '@' || $filed == '!@') { // exist or not exist
                if ($value instanceof Query) {
                    $cons [] = str_replace ( array ('!', '@' ), array ('NOT ', 'EXISTS' ), $filed );
                    $value->setBindValues ( $values );
                    $value->setDialect ( $dialect );
                    $cons [] = '(' . $value->__toString () . ')';
                } else {
                    array_shift ( $cons );
                }
            } else if (empty ( $filed ) || is_numeric ( $filed )) { // the value must be a Condition instance.
                if ($value instanceof Condition) {
                    $cons [] = '(' . $value->getWhereCondition ( $dialect, $values ) . ')';
                } else {
                    array_shift ( $cons );
                }
            } else { //others
                $ops = explode ( ' ', $filed );
                if (count ( $ops ) == 1) {
                    $filed = $ops [0];
                    $op = '=';
                } else {
                    $op = array_pop ( $ops );
                    $filed = implode ( ' ', $ops );
                }
                $op = strtoupper ( trim ( $op ) );
                $filed = $dialect->sanitize ( $dialect->getTableName ( $filed ) );
                if ($op == '$') { // null or not null
                    if ($value) {
                        $cons [] = $filed . ' IS NULL';
                    } else {
                        $cons [] = $filed . ' IS NOT NULL';
                    }
                } else if ($op == 'BETWEEN') { //between
                    $val1 = $values->addValue ( $filed, $value [0] );
                    $val2 = $values->addValue ( $filed, $value [1] );
                    $cons [] = $filed . ' BETWEEN ' . $val1 . ' AND ' . $val2;
                } else if ($op == 'IN' || $op == '!IN') { // in
                    $op = str_replace ( '!', 'NOT ', $op );
                    if ($value instanceof Query) { // sub-select as 'IN' or 'NOT IN' values.
                        $value->setBindValues ( $values );
                        $value->setDialect ( $dialect );
                        $cons [] = $filed . ' ' . $op . ' (' . $value->__toString () . ')';
                    } else if (is_array ( $value )) {
                        $vs = array ();
                        foreach ( $value as $v ) {
                            $vs [] = $values->addValue ( $filed, $v );
                        }
                        $cons [] = $filed . ' ' . $op . ' (' . implode ( ',', $vs ) . ')';
                    } else {
                        array_shift ( $cons );
                    }
                } else if ($op == 'LIKE' || $op == '!LIKE') { // like
                    $op = str_replace ( '!', 'NOT ', $op );
                    $cons [] = $filed . ' ' . $op . ' ' . $values->addValue ( $filed, $value );
                } else {
                    if ($value instanceof ImmutableValue) {
                        $val = $dialect->sanitize ( $dialect->getTableName ( $value->__toString () ) );
                    } else if ($value instanceof Query) {
                        $value->setBindValues ( $values );
                        $value->setDialect ( $dialect );
                        $val = '(' . $value->__toString () . ')';
                    } else {
                        $val = $values->addValue ( $filed, $value );
                    }
                    $cons [] = $filed . ' ' . $op . ' ' . $val;
                }
            }
        }
        if ($cons) {
            array_shift ( $cons );
            return implode ( ' ', $cons );
        }
        return '';
    }

    public function offsetExists($offset) {
        return false;
    }

    public function offsetGet($offset) {
        return null;
    }

    /**
     * (non-PHPdoc)
     * || - or
     * @ - existi
     * !@ - not exist
     * $ - null or not null
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        $this->conditions [] = array ($offset, $value );
    }

    public function offsetUnset($offset) {}
}