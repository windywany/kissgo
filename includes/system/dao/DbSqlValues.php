<?php
/**
 * 
 * sql values
 * @author Leo Ning
 *
 */
class DbSqlValues {
    private $values = array ();
    private $names = array ();
    /**
     * 
     * driver
     * @var PdoDialect
     */
    private $driver = null;
    public function __construct($driver) {
        $this->driver = $driver;
    }
    public function getValues() {
        return $this->values;
    }
    public function merge($values) {
        $this->values += $values;
    }
    public function addValue($name, $value) {
        $index = isset ( $this->names [$name] ) ? $this->names [$name] : 0;
        $key = ':' . $name . '_' . $index;
        if (is_numeric ( $value )) {
            $type = PDO::PARAM_INT;
        } else if (is_bool ( $value )) {
            $type = PDO::PARAM_BOOL;
        } else if (is_null ( $value )) {
            $type = PDO::PARAM_NULL;
        } else {
            $type = PDO::PARAM_STR;
        }
        $this->values [] = array ($key, $value, $type );
        $this->names [$name] = ++ $index;
        return $key;
    }
}