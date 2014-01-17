<?php

/**
 * update SQL
 *
 * @author guangfeng.ning
 *
 */
class UpdateSQL extends QueryBuilder implements Countable {
    private $data = array ();

    public function __construct($table) {
        parent::__construct ();
        $this->from ( $table );
    }

    /**
     * the data to be updated
     *
     * @param array $data
     * @return UpdateSQL
     */
    public function set($data) {
        $this->data += $data;
        return $this;
    }

    public function count() {
        $this->checkDialect ();
        $values = new BindValues ();
        $froms = $this->prepareFrom ( $this->sanitize ( $this->from ) );
        $sql = $this->dialect->getUpdateSQL ( $froms [0] [0], $this->data, $this->where, $values );
        if ($sql) {
            try {
                $statement = $this->dialect->prepare ( $sql );
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
                    return $statement->rowCount ();
                }
            } catch ( PDOException $e ) {
                $this->error = $e->getMessage ();
                $this->errorSQL = $sql;
                $this->errorValues = $values->__toString ();
            }
        } else {
            $this->error = 'Can not generate the delete SQL';
            $this->errorSQL = '';
            $this->errorValues = $values->__toString ();
        }
        return false;
    }

    public function execute() {
        $cnt = count ( $this );
        return empty ( $this->error ) ? true : false;
    }
}
