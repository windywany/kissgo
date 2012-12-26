<?php
/**
 * 传统的(非PDO方式)MYSQL数据源,提供MYSQL数据库访问能力
 * @author Leo 2010-09-07
 *
 */
class MysqlDatasource extends DataSource {
    private $trans = 0; //事务标志
    private $prefix = '';

    /**
     * 开启一个事务
     */
    public function begin() {
        if ($this->trans === 1) { //已经在事务中
            return true;
        } else {
            if (@mysql_query("BEGIN", $this->connection) === true) {
                $this->trans = 1;
                return true;
            } else {
                $msg = @mysql_error($this->connection);
                $this->error_msg = "Unable to begin a transaction,server messages:[$msg]";
                return false;
            }
        }
    }

    /**
     * 提交事务
     */
    public function commit() {
        if ($this->trans === 0) { //不在事务中
            return true;
        } else {
            if (@mysql_query("COMMIT", $this->connection) === true) {
                $this->trans = 0;
                return true;
            } else {
                $msg = @mysql_error($this->connection);
                $this->error_msg = "Unable to commit current transaction,server messages:[$msg]";
                return false;
            }
        }
    }

    /**
     * 连接数据库
     * @return boolean
     */
    public function connect() {
        $this->options = array_merge(array('encoding' => 'UTF8', 'prefix' => '', 'host' => 'localhost','port'=>3306, 'user' => 'root', 'password' => 'root', 'pconnect' => false), $this->options);
        extract($this->options);
        $crash = 0;
        $this->prefix = isset ($prefix) ? $prefix : '';
        $this->charset = isset ($encoding) && !empty ($encoding) ? $encoding : 'UTF8';
        if ($pconnect === true) {
            $this->connection = @mysql_pconnect($host, $user, $password) or $crash = 1;
        } else {
            $this->connection = @mysql_connect($host, $user, $password) or $crash = 1;
        }
        if ($crash == 1) {
            $msg = @mysql_error();
            $this->error_msg = "Unable to connect to MySQL server '$host' - Either your username, password are incorrect or you have not started MYSQL on your server![$msg]";
            return false;
        }
        $this->encoding();
        if (!empty ($dbname)) {
            return $this->usedb($dbname);
        }
        return true;
    }

    /**
     * 断开与数据库的连接
     * @return void
     */
    public function disconnect() {
        if ($this->connection) {
            @mysql_close($this->connection);
        }
    }

    /**
     * 设置连接编码
     * @param string $encoding
     */
    public function encoding($encoding = '') {
        if (!empty ($encoding) && !is_numeric($encoding) && $this->connection) {
            @mysql_query("SET NAMES '$encoding'", $this->connection);
            $this->charset = $encoding;
        } else if (!empty ($this->charset)) {
            @mysql_query("SET NAMES '{$this->charset}'", $this->connection);
        }
    }

    /**
     * 转换安全字符
     * @see mysql_escape_string
     * @param string $str
     * @return string
     */
    public function escape($str) {
        return mysql_real_escape_string($str);
    }

    /**
     * 执行一条SQL语句,返回影响的记录条数,如果执行出错返回false
     * @param string $sql
     * @return mixed 影响记录条数,出错返回false
     */
    public function execute($sql) {
        $crase = 0;
        $this->queryString = $sql;

        @mysql_query($sql, $this->connection) or $crase = 1;
        if ($crase === 1) {
            $msg = @mysql_error($this->connection);
            $this->error_msg = "Unable to perform sql: [$sql]\n\tserver messages:[$msg]";
            return false;
        }
        return @mysql_affected_rows($this->connection);
    }

    /**
     * 得到表全名,有可能表有前缀
     * @param string $table
     * @return string
     */
    public function full_name($table) {
        return $this->prefix . $table;
    }

    /**
     * 最新主键ID值
     * @see mysql_insert_id
     * @param string $name
     * @return int
     */
    public function last_insert_id($name = '') {
        return mysql_insert_id($this->connection);
    }

    /**
     * 查询数据库
     * @param string $sql
     * @return ResultSet 查询出错返回false
     */
    public function query($sql) {
        $this->queryString = $sql;
        $crash = 0;
        $result = @mysql_query($sql, $this->connection) or $crash = 1;
        if ($crash === 1) {
            $msg = @mysql_error($this->connection);
            $this->error_msg = "Unable to perform query: [$sql]\n\tserver messages:[$msg]";
            return false;
        }
        return new MysqlResultSet ($result, $this->connection, $this);
    }

    /**
     * 分页查询
     * @see query
     * @param string $sql 无分页SQL语句
     * @param int $start 偏移
     * @param int $limit 分页大小
     * @return ResultSet 查询出错返回false
     */
    public function queryLimit($sql, $start, $limit) {
        $start = intval($start) * intval($limit);
        $sql = $sql . " LIMIT {$start},{$limit}";
        return $this->query($sql);
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        if ($this->trans === 0) { //不在事务中
            return true;
        } else {
            if (@mysql_query("ROLLBACK", $this->connection) === true) {
                $this->trans = 0;
                return true;
            } else {
                $msg = @mysql_error($this->connection);
                $this->error_msg = "Unable to rollback current transaction,server messages:[$msg]";
                return false;
            }
        }
    }

    /**
     * 选择表
     * @param string $db
     * @return bool
     */
    public function usedb($db) {
        $crash = 0;
        @mysql_select_db($db, $this->connection) or $crash = 1;
        if ($crash == 1) {
            $msg = @mysql_error($this->connection);
            $this->error_msg = "Unable to select database: $db,server:[$msg].";
            return false;
        }
        @mysql_query("SET NAMES '$this->charset'", $this->connection);
        return true;
    }
}