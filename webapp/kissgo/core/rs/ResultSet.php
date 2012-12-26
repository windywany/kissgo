<?php
/**
 * 抽象的结果集.
 * 
 * 定义数据库查询返回结果
 * 
 * @author Leo Ning <leo.ning@like18.com>
 * @abstract 
 * @version 1.0
 * @since 1.0
 * @package model
 */
abstract class ResultSet implements Iterator, Countable, ArrayAccess {
	protected $position = 0;
	public $rows = 0;
	public $countTotal = 0;
	/**
	 * 结果集中共有多少条记录.
	 * @return int 记录数
	 */
	public function rowCount() {
		return $this->rows;
	}
	public function key() {
		return $this->position;
	}
	public function next() {
		++ $this->position;
	}
	public function rewind() {
		$this->position = 0;
	}
	public function offsetExists($offset) {
		return $offset >= 0 && $offset < $this->position;
	}
	public function offsetGet($offset) {
		$old = $this->position;
		$this->position = $offset;
		$value = $this->current ();
		$this->position = $old;
		return $value;
	}
	public function offsetSet($offset, $value) {
	
	}
	public function offsetUnset($offset) {
	
	}
	public function valid() {
		return $this->position < $this->rows;
	}
	/**
	 * 将结果集中的$field对应的值合并为一个以$sep分隔的字符串.
	 * 
	 * @param string $field
	 * @param boolean $quote 是否加引号"'"
	 * @param string  $sep  分隔字符
	 * @return string
	 */
	public function join($field, $quote = true, $sep = ",") {
		$results = array ();
		foreach ( $this as $key => $value ) {
			if (isset ( $value [$field] )) {
				if ($quote) {
					$results [] = "'{$value[$field]}'";
				} else {
					$results [] = $value [$field];
				}
			}
		}
		return join ( $sep, $results );
	}
	/**
	 * 列转行
	 * 
	 * 将$field对应的列转为一行
	 * 
	 * @param string $field 结果集中的一列字段
	 * @return array
	 */
	public function column_switch($field) {
		$results = array ();
		foreach ( $this as $key => $value ) {
			if (isset ( $value [$field] )) {
				$results [] = $value [$field];
			}
		}
		return $results;
	}
	
	/**
	 * 返回结果集的数组
	 * @param string/int $field 使用指定字段值做为数组的KEY
	 * @param string/int $vfield 使用指定字段值做为数组的值，如果为空则使用整条记录做为值
	 * @return array
	 */
	public function toArray($field = "", $vfield = "", $results = array()) {
		if (! empty ( $field ) || is_numeric ( $field )) {
			foreach ( $this as $key => $value ) {
				$key = $value [$field];
				$value = empty ( $vfield ) ? $value : $value [$vfield];
				$results [$key] = $value;
			}
		} else {
			foreach ( $this as $key => $value ) {
				$results [] = $value;
			}
		}
		return $results;
	}
	/**
	 * 得到结果集中的第几条记录
	 * @param int $index
	 */
	public function at($index = 0) {
		$index = $index >= $this->rows ? $this->rows - 1 : $index;
		$index = $index < 0 ? 0 : $index;
		$this->position = $index;
		return $this->current ();
	}
	/**
	 * $index行结果中的$field字段对应的值
	 * @param string $field 字段名
	 * @param int $index 位置索引
	 * @param mixed $default 默认值
	 * @return mixed 值
	 */
	public function fieldAt($field, $index = 0, $default = "") {
		$data = $this->at ( $index );
		if ($data !== false && isset ( $data [$field] )) {
			return $data [$field];
		}
		return $default;
	}
	/**
	 * 
	 * 将查询出来的记录中的$filed值用$sep连接起来
	 * @param string $filed
	 * @param string $sep
	 * @return string
	 */
	public function ups($field, $upfield, $upid, &$ups) {
		array_unshift ( $ups, $upid );
		foreach ( $this as $v ) {
			if ($upid != 0 && $upid == $v [$field]) {
				$this->ups ( $field, $upfield, $v [$upfield], $ups );
				return;
			}
		}
	}
	/**
	 * 返回使用记录集中的结果总数
	 * @see ResultSet::rows
	 * @return int
	 */
	public function count() {
		return $this->rows;
	}
	/**
	 * 
	 * 析构函数
	 */
	public function __destruct() {
		$this->dispose ();
	}
	/**
	 * 释放资源(结果集)
	 */
	public abstract function dispose();
}