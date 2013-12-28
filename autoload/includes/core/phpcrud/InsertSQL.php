<?php
class InsertSQL extends QueryBuilder implements Countable, ArrayAccess, IteratorAggregate {
    private $intoTable;
    private $datas;
    private $batch;
    private $ids = array ();
    private $keyField = null;
    public function __construct($datas, $batch = false) {
        parent::__construct ();
        $this->datas = $datas;
        $this->batch = $batch;
    }
    /**
     * specify the auto increment key then
     *
     * @param string $key
     * @return InsertSQL
     */
    public function autoKey($key) {
        $this->keyField = $key;
        return $this;
    }
    /**
     * the datas will be inserted into whitch table.
     *
     * @param string $table
     * @return InsertSQL
     */
    public function inito($table) {
        $this->intoTable = $table;
        return $this;
    }
    /**
     * just use count() function to perform this SQL and get the affected rows(inserted)
     *
     * @see Countable::count()
     * @return int
     */
    public function count() {
        $this->checkDialect ();
        $values = new BindValues ();
        $data = $this->batch ? $this->datas [0] : $this->datas;
        $into = $this->prepareFrom ( array (array ($this->intoTable, null ) ) );
        $sql = $this->dialect->getInsertSQL ( $into [0] [0], $data, $values );
        if ($sql) {
            try {
                $statement = $this->dialect->prepare ( $sql );
                if ($this->batch) {
                    foreach ( $this->datas as $idx=> $data ) {
                        foreach ( $values as $value ) {
                            list ( $name, $val, $type, $key ) = $value;
                            if (! $statement->bindValue ( $name, $data [$key], $type )) {
                                $this->errorSQL = $sql;
                                $this->errorValues = $values->__toString ();
                                $this->error = 'can not bind the value ' . $val . '[' . $type . '] to the argument:' . $name;
                                return false;
                            }
                        }
                        $rst = $statement->execute ();
                        if ($rst) {
                            $this->ids [$idx] = $this->dialect->lastInsertId ( $this->keyField );
                        } else {
                            break;
                        }
                    }
                    return count ( $this->ids );
                } else {
                    foreach ( $values as $value ) {
                        list ( $name, $val, $type ) = $value;
                        if (! $statement->bindValue ( $name, $val, $type )) {
                            $this->errorSQL = $sql;
                            $this->errorValues = $values->__toString ();
                            $this->error = 'can not bind the value ' . $val . '[' . $type . '] to the argument:' . $name;
                            return false;
                        }
                    }
                    $rst = $statement->execute ();
                    if ($rst) {
                        $this->ids [] = $this->dialect->lastInsertId ( $this->keyField );
                        return 1;
                    }
                }
            } catch ( PDOException $e ) {
                $this->error = $e->getMessage ();
                $this->errorSQL = $sql;
                $this->errorValues = $values->__toString ();
            }
        } else {
            $this->error = 'Can not generate the insert SQL';
            $this->errorSQL = '';
            $this->errorValues = $values->__toString ();
        }
        return false;
    }

    public function offsetExists($offset) {
        return isset ( $this->ids [$offset] );
    }

    public function offsetGet($offset) {
        return $this->ids [$offset];
    }

    public function offsetSet($offset, $value) {}

    public function offsetUnset($offset) {}
    /**
     * get the last inserted id
     *
     * @param string $name
     * @return int
     */
    public function lastId($name = null) {
        $this->checkDialect ();
        return $this->dialect->lastInsertId ( $name );
    }
    public function getIterator() {
        return new ArrayIterator ( $this->ids );
    }
}
