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
    const TYPE_EXTRA = 'extra';
    const TE_NORMAL = 'normal';
    const TE_TINNY = 'tiny';
    const TE_MIDDEM = 'medium';
    const TE_SMALL = 'small';
    const TE_BIG = 'big';
    const UNSIGNED = 'UNSIGNED';
    const NN = 'NOT NULL';
    const DEFT = 'DEFAULT';
    const CMMT = 'COMMENT';
    const AUTOUPDATE_DATE = 'AUTO_UPDATE_DATE';
    const AUTOINSERT_DATE = 'AUTO_INSERT_DATE';
    const AUTOUPDATE_UID = 'AUTO_UPDATE_UID';
    const AUTOINSERT_UID = 'AUTO_INSERT_UID';
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
    public function save($data, $alias = null);
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
     * delete
     * @param array $condition
     */
    public function delete($alias = null);
    public function exist($data);
    public function insert($data);
    public function update($data, $where);
    public function remove($where);
    public function lastId($name = null);
    /**
     * 
     * return the full name of Idao
     * @return string
     */
    public function getFullTableName();
    public function getAlias();
    /**
     * 
     * prepare select fields
     * @param array $fields
     * @param DbSqlValues
     */
    public static function prepareFields($fields, &$values, $specialChar, $dao = null);
    /**
     * 
     * create table sql
     * @return string
     */
    public function getCreateSql();
}
$__kissgo_db_error = array ();
/**
 * set or get the database error
 * @param mixed $error
 * @return mixed
 */
function db_error($error = null) {
    global $__kissgo_db_error;
    if (is_string ( $error )) {
        $__kissgo_db_error [] = $error;
        log_warn ( $error );
    } else if (! empty ( $__kissgo_db_error )) {
        $msg = implode ( "\n", $__kissgo_db_error );
        return $error === true ? nl2br ( $msg ) : $msg;
    }
}