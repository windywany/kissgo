<?php
/**
 * MYSQL 数据库结果集
 * @author Leo 2010-04-16
 * @version 1.0
 * @since 1.0
 */
class MysqlResultSet extends ResultSet {
	protected $resultset = null;
	protected $connection;
    /**
     * @var Datasource
     */
    protected $datasource;

    /**
     * @param $result
     * @param $connection
     * @param Datasource $datasource
     */
    public function __construct($result, $connection, $datasource) {
		$this->position = 0;
		$this->connection = $connection;
		$this->datasource = $datasource;
		if ($result) {
			$this->resultset = $result;
			$this->rows = @mysql_num_rows ( $result );
			$this->rows = $this->rows === false ? 0 : $this->rows;
		} else {
			$this->rows = 0;
		}
	}
	/**
	 * 是否可用
	 * @return boolean
	 */
	public function valid() {
		return $this->resultset && parent::valid ();
	}
	/**
	 * 返回当前结果,array('field'=>'value')形式的数组
	 * @return mixed
	 */
	public function current() {
		$crase = 0;
		@mysql_data_seek ( $this->resultset, $this->position ) or $crase = 1;
		if ($crase === 1) {
			$msg = @mysql_error ( $this->connection );
			$this->datasource->last_error_msg ( "Unable to seek data at row:[$this->position],server messages:[$msg]" );
			return false;
		}
		$crase = 0;
		$data = @mysql_fetch_array ( $this->resultset, MYSQL_ASSOC ) or $crase = 1;
		if ($crase === 1) {
			$msg = @mysql_error ( $this->connection );
			$this->datasource->last_error_msg ( "Unable to fetch data at row:[$this->position],server messages:[$msg]" );
			return false;
		}
		return $data;
	}
	/**
	 * 释放MYSQL结果集
	 */
	public function dispose() {
		if ($this->resultset && is_object ( $this->resultset )) {
			@mysql_free_result ( $this->resultset );
		}
	}
}