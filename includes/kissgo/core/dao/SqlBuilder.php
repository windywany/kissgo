<?php
interface SqlBuilder {
    public function select($from, $fields, $join, $condition, $group, $order, $having, $limit);
    public function update($table, $data, $condition);
    public function insert($table, $data);
    public function delete($table, $where);
    public function schema($schema);
    public function buildCondition($condition, $needAlias = true);
}