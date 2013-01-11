<?php
/**
 * 
 * 数据库事务
 * @author Leo Ning
 *
 */
class DbTrans {
    private static $dts = array ();
    private $_ds = null;
    private function __construct($datasource) {
        $this->_ds = DataSource::getDataSource ( $datasource );
        if ($this->_ds == null) {
            trigger_error ( DataSource::getLastError ( $datasource ), E_USER_ERROR );
        }
        if (! $this->_ds->begin ()) {
            trigger_error ( DataSource::getLastError ( $datasource ), E_USER_WARNING );
        }
    }
    /**
     * 开启事务.
     *
     * 在一次事务中,只需要一个model开启即可
     *
     * @return DbTrans 返回DbTrans实例
     */
    public static function begin($ds = 'default') {
        if (! isset ( self::$dts [$ds] )) {
            self::$dts [$ds] = new DbTrans ( $ds );
        }
        return self::$dts [$ds];
    }
    
    /**
     * 
     * 提交事务.
     *
     * 只需要一个model提交即可,通常由开启事务的那个model提交
     *
     * @return boolean 成功返回true,失败返回false
     */
    public function commit() {
        return $this->_ds->commit ();
    }
    
    /**
     * 
     * 回滚事务.
     *
     * 只要一个model回滚即可,通常由开启事务的那个model回滚
     *
     * @return boolean 成功返回true,失败返回false
     */
    public function rollback() {
        return $this->_ds->rollback ();
    }
}