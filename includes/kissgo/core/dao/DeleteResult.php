<?php
/**
 * 
 * 删除结果
 * @author Leo Ning
 *
 */
class DeleteResult extends DbSqlHelper implements Countable {
    private $dao = null;
    private $alias = null;
    private $builder = null;
    private $driver = null;
    /**
     * 
     * 
     * @param array $data
     * @param Idao $dao
     */
    public function __construct($dao, $alias = null) {
        $this->dao = $dao;
        $this->driver = $dao->getDriver ();
        $this->alias = $alias;
        $this->builder = $this->driver->getSqlBuilder ();
    }
    public function count() {
        $sql = $this->builder->delete ( array ($this->dao, $this->alias ), $this );
        if ($sql) {
            try {
                return $sql->execute ( $this->driver );
            } catch ( Exception $e ) {
                $this->errorInfo = $this->driver->errorInfo ();
                log_debug ( $e->getMessage () . ' [' . $sql . ']' );
                //throw $e;
            }
        }
        return 0;
    }
}