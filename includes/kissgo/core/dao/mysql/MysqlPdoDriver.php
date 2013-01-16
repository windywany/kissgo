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
        return array ('mysql:dbname=kissgo;host=127.0.0.1;charset=UTF-8', 'root', 'root', null );
    }
    public function specialChar() {
        return '`';
    }
    public function schema($schema) {}
    public function delete($table, $condition) {}
    public function insert($table, $data) {}
    public function select($from, $fields, $join, $condition, $group, $order, $having, $limit) {
        list ( $dao, $alias ) = $from;
        $fields = $dao->prepareFields ( $fields );
        if (! $fields) {
            throw new PDOException ( "field lists is empty" );
        }
        $fname = $dao->getFullTableName ();
        $sql = "SELECT $fields FROM `$fname` AS `$alias`";
        if (! empty ( $join )) {
            $sql .= $this->buildJoin ( $join );
        }
        if (! empty ( $condition )) {
            $sql .= $this->buildWhere ( $condition );
        }
        if (! empty ( $group )) {
            $sql .= $this->buildGroupBy ( $group );
        }
        if (! empty ( $order )) {
            $sql .= $this->buildOrder ( $order );
        }
        if (! empty ( $having )) {
            $sql .= $this->buildHaving ( $having );
        }
        if (! empty ( $limit )) {
            $sql .= $this->buildLimit ( $limit );
        }
        return new DbSQL ( $sql );
    }
    public function update($table, $data, $condition) {}
    /**
     * 生成join语句
     * @param array $joins
     */
    private function buildJoin($joins) {}
}