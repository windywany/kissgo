<?php
/**
 * 
 * 数据库表
 * @author Leo Ning
 *
 */
abstract class DbTable extends DbView {
    /**
     * (non-PHPdoc)
     * @see DbView::delete()
     * @return DeleteResult
     */
    public function delete($alias = null) {
        $alias = $alias == null ? $this->alias : $alias;
        return new DeleteResult ( $this, $alias );
    }
    /**
     * (non-PHPdoc)
     * @see DbView::save()
     * @return ExecuteResult
     */
    public function save($data, $alias = null) {
        $alias = $alias == null ? $this->alias : $alias;
        return new ExecuteResult ( $this, $data, $alias );
    }
    /**
     * (non-PHPdoc)
     * @see Idao::lastId()
     */
    public function lastId($name = null) {
        return $this->driver->lastInsertId ( $name );
    }
}