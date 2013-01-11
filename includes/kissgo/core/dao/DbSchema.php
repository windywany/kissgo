<?php
class DbSchema implements IteratorAggregate {
    protected $description = '';
    protected $fields = array ();
    public function __construct($description = '') {
        $this->description = $description;
    }
    public function getIndexes() {

    }
    public function getPrimaryKey() {

    }
    public function addIndex($name, $fields, $sort = "") {

    }
    public function addUnique($name, $fields) {

    }
    public function addFullTextIndex($name, $fields) {

    }
    public function getIterator() {
        return new ArrayIterator ( $this->fields );
    }
}