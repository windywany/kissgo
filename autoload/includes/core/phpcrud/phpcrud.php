<?php
/**
 * execute the $callable in a transaction
 * 
 * @param mixed $callable
 * @param array $param
 * @param string $name
 * @return mixed
 */
function runintran($callable, $param = array(), $name = null) {
    if (start_tran ( $name )) {
        if (call_user_func_array ( $callable, $param )) {
            return commit_tran ( $name );
        } else {
            rollback_tran ( $name );
        }
    }
    return false;
}
/**
 * start a database transaction
 * 
 * @param string $name database
 * @return boolean
 */
function start_tran($name = null) {
    $dialect = DatabaseDialect::getDialect ( $name );
    return $dialect->beginTransaction ();
}
/**
 * commit a transaction
 * 
 * @param string $name
 */
function commit_tran($name = null) {
    $dialect = DatabaseDialect::getDialect ( $name );
    try {
        return $dialect->commit ();
    } catch ( PDOException $e ) {
        return false;
    }
}
/**
 * rollback a transaction
 * 
 * @param string $name
 */
function rollback_tran($name = null) {
    $dialect = DatabaseDialect::getDialect ( $name );
    try {
        return $dialect->rollBack ();
    } catch ( PDOException $e ) {
        return false;
    }
}
/**
 * insert data into table
 * 
 * @param array $datas
 * @param array $batch
 */
function dbinsert($datas, $batch = false) {
    return new InsertSQL ( $datas, $batch );
}
/**
 * shortcut for new Query
 * 
 * @param string $fields
 * @return Query
 */
function dbselect($fields = '*') {
    return new Query ( func_get_args () );
}
/**
 * update data
 * 
 * @param string $table
 * @return UpdateSQL
 */
function dbupdate($table) {
    return new UpdateSQL ( $table );
}
/**
 * delete data from table(s)
 * 
 * @return DeleteSQL
 */
function dbdelete() {
    return new DeleteSQL ( func_get_args () );
}
/**
 * short call for creating a ImmutableValue instance
 * 
 * @param string $val
 * @return ImmutableValue
 */
function imv($val) {
    return new ImmutableValue ( $val );
}
//end of phpcrud.php