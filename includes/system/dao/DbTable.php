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
     * @see DbView::insert()
     */
    public function insert($data) {
        $saver = $this->save ( $data );
        if (count ( $saver ) > 0) {
            return $saver->getData ();
        }
        return false;
    }
    /**
     * (non-PHPdoc)
     * @see DbView::remove()
     */
    public function remove($where) {
        $deletor = $this->delete ()->where ( $where );
        return count ( $deletor ) >= 0;
    }
    /**
     * (non-PHPdoc)
     * @see DbView::update()
     */
    public function update($data, $where) {
        $saver = $this->save ( $data )->where ( $where );
        return count ( $saver ) >= 0;
    }
    /**
     * (non-PHPdoc)
     * @see Idao::lastId()
     */
    public function lastId($name = null) {
        return $this->driver->lastInsertId ( $name );
    }
}