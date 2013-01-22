<?php
/**
 * 
 * 执行结果(新增，更新)
 * @author Leo Ning
 *
 */
class ExecuteResult extends DbSqlHelper implements Countable {
    private $data = null;
    private $dao = null;
    private $alias = null;
    private $builder = null;
    private $driver = null;
    private $sChar = null;
    /**
     * 
     * 
     * @param array $data
     * @param Idao $dao
     */
    public function __construct($dao, $data = array(), $alias = null) {
        $this->data = $data;
        $this->dao = $dao;
        $this->alias = $alias == null ? $dao->getAlias () : $alias;
        $this->driver = $dao->getDriver ();
        $this->builder = $this->driver->getSqlBuilder ();
        $this->sChar = $this->builder->specialChar ();
    }
    
    public function count() {
        $schema = $this->dao->schema ();
        if (! empty ( $this->condition )) {
            $schema->getAutoUpdateData ( $this->data, $this->alias, $this->sChar );
            $sql = $this->builder->update ( array ($this->dao, $this->alias ), $this->data, $this );
        } else {
            $schema->getAutoInsertData ( $this->data );
            $schema->getAutoUpdateData ( $this->data, '', '' );
            $sql = $this->builder->insert ( array ($this->dao, $this->alias ), $this->data );
        }
        if ($sql) {
            try {
                return $sql->execute ( $this->driver );
            } catch ( PDOException $e ) {
                $this->errorInfo = $this->driver->errorInfo ();
                log_debug ( $e->getMessage () . ' [' . $sql . ']' );
                throw $e;
            }
        }
        return 0;
    }

}