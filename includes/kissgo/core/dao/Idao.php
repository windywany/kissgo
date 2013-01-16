<?php
/**
 * 
 * database access object interface
 * @author Leo Ning
 *
 */
interface Idao {
    const TYPE = 'type';
    const TYPE_CHAR = 'char';
    const TYPE_SERIAL = 'serial';
    const TYPE_INT = 'int';
    const TYPE_TEXT = 'text';
    const TYPE_BOOL = 'bool';
    const TYPE_BLOB = 'blob';
    const TYPE_FLOAT = 'float';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_VARCHAR = 'varchar';
    const TYPE_NUMERIC = 'numeric';
    const LENGTH = 'length';
    const TYPE_EXTRA = 'EXTRA';
    const TE_NORMAL = 'normal';
    const TE_TINNY = 'tiny';
    const TE_MIDDEM = 'medium';
    const TE_SMALL = 'small';
    const TE_BIG = 'big';
    const UNSIGNED = 'UNSIGNED';
    const NN = 'NOT NULL';
    const DEFT = 'DEFAULT';
    const CMMT = 'COMMENT';
    const AUTOUPDATE = 'AUTO_UPDATE';
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