<?php
/**
 * SQL 
 * @author Leo
 *
 */
class DbSQL {
    protected $values;
    protected $sql;
    protected $options = array ();
    protected $stmt = null;
    public function __construct($sql, $values = null, $options = array()) {
        $this->sql = $sql;
        $this->values = $values;
        $this->options = $options;
    }
    public function __destruct() {
        if ($this->stmt) {            
            $this->stmt = null;
        }
    }
    /**
     * @param PdoDialect $driver
     * @return PDOStatement
     */
    public function query($driver, $values = null) {
        $values = $values ? $values : $this->values;
        try {
            $this->options [PDO::ATTR_CURSOR] = PDO::CURSOR_SCROLL;
            if (! $this->stmt) {
                $this->stmt = $driver->prepare ( $this->sql, $this->options );
            }
            if ($values && is_array ( $values )) {
                foreach ( $values as $value ) {
                    list ( $name, $val, $type ) = $value;
                    if (! $this->stmt->bindValue ( $name, $val, $type )) {
                        db_error ( 'Can not bind value: name:' . $name . ', value:' . $val . ', type:' . $type );
                    }
                }
            }
            $rst = $this->stmt->execute ();
            if ($rst) {
                return $this->stmt;
            } else {
                $info = $driver->errorInfo ();
                db_error ( $info [2] . "\nSQL:" . $this->sql . "\nVALUES: <pre>" . var_export ( $values, true ) .'</pre>');
            }
        } catch ( Exception $e ) {
            db_error ( $e->getMessage () . "\nSQL:" . $this->sql . "\nVALUES: <pre>" . var_export ( $values, true ).'</pre>' );
        }
        return array ();
    }
    /**
     * 取值或设置值
     * @param array $values
     * @return array
     */
    public function values($values = array()) {
        if (! empty ( $values )) {
            $this->values = $values;
        }
        return $this->values;
    }
    /**
     * 
     * 执行一条SQL
     * @param PdoDialect $driver
     * @param array $values
     * @return int
     */
    public function execute($driver, $values = null) {
        $values = $values ? $values : $this->values;
        try {
            if (! $this->stmt) {
                if (! empty ( $this->options )) {
                    $this->stmt = $driver->prepare ( $this->sql, $this->options );
                } else {
                    $this->stmt = $driver->prepare ( $this->sql );
                }
            }
            if ($values && is_array ( $values )) {
                foreach ( $values as $value ) {
                    list ( $name, $val, $type ) = $value;
                    $this->stmt->bindValue ( $name, $val, $type );
                }
            }
            $rst = $this->stmt->execute ();
            if ($rst) {
                return $this->stmt->rowCount ();
            } else {
                $info = $driver->errorInfo ();
                db_error ( $info [2] . "\nSQL:" . $this->sql . "\nVALUES: <pre>" . var_export ( $values, true ).'</pre>' );
            }
        } catch ( Exception $e ) {
            db_error ( $e->getMessage () . "\nSQL:" . $this->sql . "\nVALUES:<pre>" . var_export ( $values, true ).'</pre>' );
        }
        return false;
    }
    public function __toString() {
        return $this->sql;
    }
}
/**
 * SQL 构建器，不同的数据库需要提供不同的构建器
 * @author Leo
 *
 */
interface SqlBuilder {
    /**
     * 
     * @param array $from
     * @param DbSqlHelper $sqlHelper
     * @return DbSQL
     */
    public function select($from, $sqlHelper);
    /**
     * 
     * @param string $table
     * @param array $data
     * @param DbSqlHelper $sqlHelper
     * @return DbSQL
     */
    public function update($table, $data, $sqlHelper);
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
     * @param DbSqlHelper $sqlHelper
     * @return DbSQL
     */
    public function delete($table, $sqlHelper);
    /**
     * 
     * @param Idao $schema
     * @return DbSQL
     */
    public function schema($dao, $engine = 'InnoDB', $charset = 'UTF8');
    /**
     * @return string
     */
    public function specialChar();
    /**
     * generate a pagination sql
     * 
     * @param string $sql a SELECT SQL statement
     * @param int $start 
     * @param int $limit
     * @return string
     */
    public function page_sql($sql,$start,$limit);
}