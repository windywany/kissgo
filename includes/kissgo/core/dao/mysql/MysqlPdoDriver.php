<?php
/**
 * 
 * Mysql Pdo Driver
 * @author Leo Ning
 *
 */
class MysqlPdoDriver extends PdoDriver implements SqlBuilder {
    /**
     * (non-PHPdoc)
     * @see PdoDriver::getSqlBuilder()
     * @return SqlBuilder
     */
    public function getSqlBuilder() {
        return $this;
    }
    /* (non-PHPdoc)
     * @see PdoDriver::buildOptions()
     */
    public function buildOptions($options) {
        return array ('mysql:dbname=kissgodb;host=127.0.0.1;charset=UTF-8', 'root', '888888', null );
    }
    public function specialChar() {
        return '`';
    }
    public function schema($schema) {

    }
    public function delete($table, $condition) {

    }
    public function insert($table, $data) {

    }
    
    public function select($from, $fields, $join, $condition, $group, $order, $having, $limit) {
        list ( $dao, $alias ) = $from;
        $fields = $dao->prepareFields ( $fields );
        if (! $fields) {
            throw new PDOException ( "SELECT fields is empty" );
        }
        $fname = $dao->getFullTableName ();
        $sql = "SELECT $fields FROM `$fname` AS `$alias`";
        
        return new DbSQL ( $sql );
    }
    public function update($table, $data, $condition) {

    }
}