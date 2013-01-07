<?php
/**
 * PDO MYSQL数据源,提供MYSQL数据库访问能力
 * @author Leo 2010-11-16
 *
 */
class PdoMysqlDatasource extends DataSource {
    private $trans = 0;
    /**
     * @var PDO
     */
    protected $pdo;
    private $prefix = '';

    /**
     * 开启一个事务
     */
    public function begin() {
        if ($this->trans === 1) { //已经在事务中
            return true;
        } else if ($this->pdo) {
            if ($this->pdo->beginTransaction()) {
                $this->trans = 1;
                return true;
            }
            $this->error_msg = implode(' ', $this->pdo->errorInfo());
        } else {
            $this->error_msg = 'The pdo object is null!';
        }
        return false;
    }

    /**
     * 提交事务
     */
    public function commit() {
        if ($this->trans === 0) { //不在事务中
            return true;
        } else if ($this->pdo) {
            if ($this->pdo->commit()) {
                $this->trans = 0;
                return true;
            }
            $this->error_msg = implode(' ', $this->pdo->errorInfo());
        } else {
            $this->error_msg = 'The pdo object is null!';
        }
        return false;
    }

    /**
     * 连接数据库
     * @return boolean 成功返回true,失败返回false
     */
    public function connect() {
        $this->options = array_merge(array('encoding' => 'UTF8', 'prefix' => '', 'host' => 'localhost', 'port' => 3306, 'user' => 'root', 'password' => 'root', 'driver_options' => array()), $this->options);
        extract($this->options);
        $this->prefix = isset ($prefix) ? $prefix : '';
        $this->charset = isset ($encoding) && !empty ($encoding) ? $encoding : 'UTF8';
        $dsn = "mysql:dbname={$dbname};host={$host};port={$port}";
        try {
            $this->pdo = new PDO ($dsn, $user, $password, $driver_options);
            $this->encoding();
            return true;
        } catch (PDOException $e) {
            $this->error_msg = $e->getMessage();
            return false;
        }
    }

    /**
     * 断开与数据库的连接
     * @return void
     */
    public function disconnect() {
        if ($this->pdo) {
            $this->pdo = null;
        }
    }

    /**
     * 设置连接编码
     * @param string $encoding
     */
    public function encoding($encoding = '') {
        if (!empty ($encoding) && !is_numeric($encoding) && $this->pdo) {
            $this->pdo->query("SET NAMES '$encoding'");
            $this->charset = $encoding;
        } else if (!empty ($this->charset)) {
            $this->pdo->query("SET NAMES '{$this->charset}'");
        }
    }

    /**
     * 转换安全字符
     * @see mysql_escape_string
     * @param string $str
     * @return string
     */
    public function escape($str) {
        if ($this->pdo) {
            return $this->pdo->quote($str);
        }
        return $str;
    }

    /**
     * 执行一条SQL语句,返回影响的记录条数,如果执行出错返回false
     * @param string $sql
     * @return mixed 影响记录条数,出错返回false
     */
    public function execute($sql) {
        $this->queryString = $sql;
        if ($this->pdo) {
            $rst = $this->pdo->exec($sql);
            if ($rst === false) {
                $emsg = $this->pdo->errorInfo();
                if (is_array($emsg)) {
                    $this->error_msg = "Unable to perform query: [$sql]\n\tserver messages:" . implode(' ', $emsg);
                }
            }
        } else {
            $rst = false;
            $this->error_msg = 'The pdo object is null!';
        }
        return $rst;
    }

    /**
     * 表全名,即添加前缀的后的名称
     * @param string $tablename 表名称
     */
    public function full_name($table) {
        return $this->prefix . $table;
    }

    /**
     * 最新主键ID值
     * @see mysql_insert_id
     * @return int
     */
    public function last_insert_id($name = '') {
        if ($this->pdo) {
            return $this->pdo->lastInsertId();
        } else {
            return null;
        }
    }

    /**
     * 查询数据库
     * @param string $sql
     * @return ResultSet 查询出错返回false
     */
    public function query($sql) {
        $this->queryString = $sql;
        if ($this->pdo) {
            $psm = $this->pdo->query($sql);
            if ($psm) {
                return new PdoResultSet ($psm);
            } else {
                $this->error_msg = "Unable to perform query: [$sql]\n\tserver messages:" . implode(' ', $this->pdo->errorInfo());
            }
        } else {
            $this->error_msg = 'The pdo object is null!';
        }
        return false;
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
        $start = $start * $limit;
        $sql = $sql . " LIMIT {$start},{$limit}";
        return $this->query($sql);
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        if ($this->trans === 0) { //不在事务中
            return true;
        } else if ($this->pdo) {
            if ($this->pdo->rollBack()) {
                $this->trans = 0;
                return true;
            }
            $this->error_msg = implode(' ', $this->pdo->errorInfo());
        } else {
            $this->error_msg = 'The pdo object is null!';
        }
        return false;
    }

    /**
     * 选择表
     * @param string $db
     */
    public function usedb($db) {
        $this->pdo = null;
        $this->options ['database'] = $db;
        return $this->connect();
    }
}