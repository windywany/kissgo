<?php
/**
 * PDO MYSQL 数据库结果集
 * @author Leo 2010-11-16
 * @version 1.0
 * @since 1.0
 */
class PdoResultSet extends ResultSet {
    protected $pms = null;
    protected $results = array();

    /**
     *
     * @param PDOStatement  $pms
     */
    public function __construct($pms) {
        $this->position = 0;
        if ($pms) {
            $this->pms = $pms;
            $this->results = $pms->fetchAll(PDO::FETCH_ASSOC);
            $this->rows = count($this->results);
            $pms->closeCursor();
        } else {
            $this->rows = 0;
        }
    }

    public function current() {
        return $this->results [$this->position];
    }

    /**
     * 是否可用
     * @return boolean
     */
    public function valid() {
        return !empty ($this->results) && parent::valid();
    }

    /**
     * 释放MYSQL结果集
     */
    public function dispose() {
        if ($this->pms) {
            $this->pms = null;
        }
    }
}