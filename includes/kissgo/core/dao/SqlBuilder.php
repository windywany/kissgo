<?php
/**
 * SQL 
 * @author Leo
 *
 */
class DbSQL {
    protected $values;
    protected $sql;
    protected $options;
    protected $prepareble = false;
    protected $stmt = null;
    public function __construct($sql, $values = null, $options = array()) {
        $this->sql = $sql;
        $this->values = $values;
        $this->options = $options;
    }
    public function prepareble($prepareble = '') {
        if ($prepareble === true) {
            $this->prepareble = $prepareble;
        }
        return $this->prepareble;
    }
    /**
     * @param PdoDriver $driver
     * @return PDOStatement
     */
    public function query($driver, $values = null) {
        try {
            if ($this->prepareble) {
                $values = $values ? $values : $this->values;
                if (! $this->stmt) {
                    if (! empty ( $this->options )) {
                        $this->stmt = $driver->prepare ( $this->sql, $this->options );
                    } else {
                        $this->stmt = $driver->prepare ( $this->sql );
                    }
                }
                if ($values && is_array ( $values )) {
                    foreach ( $values as $value ) {
                        list ( $name, $value, $type ) = $value;
                        $this->stmt->bindValue ( $name, $value, $type );
                    }
                }
                $rst = $this->stmt->execute ();
                if ($rst) {
                    return $this->stmt;
                } else {
                    $info = $driver->errorInfo ();
                    throw new PDOException ( $info [2] );
                }
            } else {
                $rst = $driver->query ( $this->sql );
                if ($rst) {
                    return $rst;
                } else {
                    $info = $driver->errorInfo ();
                    throw new PDOException ( $info [2] );
                }
            }
        } catch ( Exception $e ) {
            throw $e;
        }
    }
    public function values($values = array()) {
        if (! empty ( $values )) {
            $this->values = $values;
        }
        return $this->values;
    }
    public function execute($driver, $values = null) {
        try {
            if ($this->prepareble) {
                $values = $values ? $values : $this->values;
                if (! $this->stmt) {
                    if (! empty ( $this->options )) {
                        $this->stmt = $driver->prepare ( $this->sql, $this->options );
                    } else {
                        $this->stmt = $driver->prepare ( $this->sql );
                    }
                }
                if ($values && is_array ( $values )) {
                    foreach ( $values as $value ) {
                        list ( $name, $value, $type ) = $value;
                        $this->stmt->bindValue ( $name, $value, $type );
                    }
                }
                $rst = $this->stmt->execute ();
                if ($rst) {
                    return $this->stmt->rowCount ();
                } else {
                    $info = $driver->errorInfo ();
                    throw new PDOException ( $info [2] );
                }
            } else {
                $rst = $driver->exec ( $this->sql );
                if ($rst === false) {
                    $info = $driver->errorInfo ();
                    throw new PDOException ( $info [2] );
                } else {
                    return $rst;
                }
            }
        } catch ( Exception $e ) {
            throw $e;
        }
    }
    public function __toString() {
        return $this->sql;
    }
}
/**
 * SQL 构建器
 * @author Leo
 *
 */
interface SqlBuilder {
    /**
     * 
     * @param array $from
     * @param array $fields
     * @param array $join
     * @param array $condition
     * @param array $group
     * @param array $order
     * @param array $having
     * @param array $limit
     * @return DbSQL
     */
    public function select($from, $fields, $join, $condition, $group, $order, $having, $limit);
    /**
     * 
     * @param string $table
     * @param array $data
     * @param array $condition
     * @return DbSQL
     */
    public function update($table, $data, $condition);
    /**
     * 
     * @param string $table
     * @param array $data
     * @return DbSQL
     */
    public function insert($table, $data);
    /**
     * 
     * @param string $table
     * @param array $condition
     * @return DbSQL
     */
    public function delete($table, $condition);
    /**
     * 
     * @param Idao $schema
     * @return DbSQL
     */
    public function schema($schema);
    /**
     * @return string
     */
    public function specialChar();
}