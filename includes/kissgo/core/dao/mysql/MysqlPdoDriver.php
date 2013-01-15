<?php
class MysqlSqlBuilder extends BasicSqlBuilder {
    /* (non-PHPdoc)
     * @see SqlBuilder::schema()
     */
    public function schema($schema) {}
    
    /* (non-PHPdoc)
     * @see SqlBuilder::select()
     */
    public function select($from, $fields, $join, $condition, $group, $order, $having, $limit) {
        return "";
    }
    public function specialChar() {
        return '`';
    }
}
class MysqlPdoDriver extends PdoDriver {
    public function getSqlBuilder() {
        static $builder = false;
        if (! $builder) {
            $builder = new MysqlSqlBuilder ();
        }
        return $builder;
    }
    /* (non-PHPdoc)
     * @see PdoDriver::buildOptions()
     */
    public function buildOptions($options) {
        return array ('mysql:dbname=kissgodb;host=127.0.0.1;charset=UTF-8', 'root', '888888', null );
    }
}