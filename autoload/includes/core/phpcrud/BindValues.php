<?php
/**
 * bind values for Pdo PrepareStatement.
 * 
 * @author guangfeng.ning
 *
 */
class BindValues implements IteratorAggregate {
    private $names = array ();
    private $values = array ();
    public function addValue($field, $value) {
        $index = isset ( $this->names [$field] ) ? $this->names [$field] : 0;
        $key = ':' . str_replace ( '.', '_', $field ) . '_' . $index;
        if (is_numeric ( $value )) {
            $type = PDO::PARAM_INT;
        } else if (is_bool ( $value )) {
            $type = PDO::PARAM_INT;
            $value = intval ( $value );
        } else if (is_null ( $value )) {
            $type = PDO::PARAM_NULL;
        } else {
            $type = PDO::PARAM_STR;
        }
        $this->values [] = array ($key, $value, $type, $field );
        $this->names [$field] = ++ $index;
        return $key;
    }
    public function getIterator() {
        return new ArrayIterator ( $this->values );
    }
    public function __toString() {
        $valString = array ();
        foreach ( $this->values as $val ) {
            $valString [] = $val [0] . ' = ' . $val [1] . ' [' . $val [2] . ']';
        }
        return implode ( '; ', $valString );
    }
}