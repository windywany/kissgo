<?php
/**
 * 
 * database access object interface
 * @author Leo Ning
 *
 */
interface Idao {
    /**
     * 
     * @return PdoDriver
     */
    public function getDriver();
    /**
     * 
     * @return DbSchema
     */
    public function schema();
    /**
     * 
     * @param array $data
     * @return array
     */
    public function save($data);
    /**
     * 
     * query data from database
     * @param string $fields
     * @param string $alias
     * @return ResultCursor
     */
    public function query($fields = '*', $alias = null);
    /**
     * 
     * new a record
     * @param array $data
     * @return array
     */
    public function create($data);
    /**
     * 
     * update some records
     * @param data $data
     * @param array $condition
     * @return int 
     */
    public function update($data, $condition);
    /**
     * 
     * delete
     * @param array $condition
     */
    public function delete($condition);
    /**
     * 
     * return the full name of Idao
     * @return string
     */
    public function getFullTableName();
    /**
     * 
     * prepare select fields
     * @param array $fields
     */
    public function prepareFields($fields);
}