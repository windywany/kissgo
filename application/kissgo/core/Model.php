<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-10-10 上午11:26
 * $Id$
 */
/**
 * 模型
 */
abstract class Model {
    /**
     * #@+
     *
     * @access public
     * @var string
     */
    /**
     * 左连接
     */
    const LEFT = 'LEFT';
    /**
     * 右连接
     */
    const RIGHT = 'RIGHT';
    /**
     * 内连接
     */
    const INNER = 'INNER';
    /**
     * 在使用where_helper时可以指定为默认值，当值为此值时，不设置此条件
     *
     * @var string
     */
    const NOTSET = '_WNT_';
    /**
     * #@-
     */
    /**
     * 模型使用的数据源
     *
     * @var DataSource
     */
    protected $_ds = NULL;
    /**
     * #@+
     *
     * @access protected
     * @var string
     */
    /**
     * 指定模型对应的表名
     */
    var $_table = '';
    /**
     * 表全名,添加前缀后的表名
     */
    protected $_ftable = '';
    /**
     * 主键
     */
    protected $_pk = '';
    /**
     * 全部字段
     */
    protected $_all = '*';
    /**
     * 实体名
     */
    protected $_entity_name;
    protected $_old_entity_name;
    /**
     * 记数字段,胜于count操作.
     */
    protected $_countfield;
    /**
     * 条件语句,由where生成的查询条件
     */
    protected $_whereString = '';
    /**
     * #@-
     */
    /**
     * #@+
     *
     * @access protected
     * @var array
     */
    /**
     * 实体字段
     */
    protected $_fields = array();
    /**
     * 查询选项
     */
    protected $_retrieveOptions = array();
    /**
     * 连接
     */
    protected $_joins = array();
    /**
     * 读取数据,用于标识本次查询是否检索数据
     */
    protected $_retrieveit = true;
    /**
     * 查询总数,是否查询总数
     */
    protected $_countit = false;
    /**
     * #@-
     */
    /**
     * count查询总数
     *
     * @var int
     * @access protected
     */
    protected $_total = 0; // 总数
    protected $_cfields = array();
    protected $_queryString = '';
    protected $_duration = 0;

    /**
     * 构造函数
     *
     * @param string $ds
     *            数据源名称
     * @uses fire
     */
    public function __construct($ds = 'default') {
        $this->_ds = DataSource::getDataSource($ds);
        if ($this->_ds == null) {
            trigger_error('can not connect to the database server!', E_USER_ERROR);
        }
        // 获取表名
        $this->_entity_name = preg_replace('/Model$/', '', get_class($this));
        $this->_old_entity_name = $this->_entity_name;
        if (empty ($this->_table)) {
            $this->_table = strtolower($this->_entity_name);
        }
        if ($this->_ds != null) {
            $this->_ftable = $this->_ds->full_name($this->_table);
        }
        $this->schema();
    }

    /**
     *
     * @param array $names
     *            用于构建查询条件的字段名,当使用别名时请使用as
     * @param array $data
     *            引用传入的数据，将从表单获取的数据填充
     * @param array $extra
     *            查询附加说明
     * @return array
     */
    public static function where_build($names, &$data = null, $extra = array()) {
        $where = array();
        $rst = Request::getInstance();
        foreach ($names as $name => $value) {
            if (is_numeric($name)) {
                $name = $value;
                $value = null;
            }
            $names = explode('as', $name);
            if (count($names) > 1) { // uname as U.name
                $name_as = trim(array_shift($names)); // 用于从表单取数据
            } else {
                unset ($name_as);
            }
            $names = preg_split('#\s+#', trim($names [0]));
            $field = array_shift($names); // 用于数据库字段
            $name = isset ($name_as) ? $name_as : $field;
            $op = empty ($names) ? '' : ' ' . $names [0];
            $ext = isset ($extra [$name]) ? $extra [$name] : '';
            if (isset($rst[$name])) {
                $value = $rst->get($name, trim($value));
                if ($value == null || $value == '') {
                    if (is_array($data)) {
                        $data [$name] = '';
                    }
                    continue;
                }
                if (is_array($data)) {
                    $data [$name] = $value;
                }
                switch ($ext) {
                    case 'like' :
                        $value = "%{$value}%";
                        break;
                    case 'llike' :
                        $value = "%{$value}";
                        break;
                    case 'rlike' :
                        $value = "{$value}%";
                        break;
                    case 'safeids' :
                        $value = safe_ids($value, ',', true);
                        break;
                    default :
                        ;
                }
                $where [$field . $op] = $value;
            } else if (is_array($data)) {
                $data [$name] = $value;
                if (!is_null($value)) {
                    $where [$field . $op] = $value;
                }
            }
        }
        return $where;
    }

    /**
     * 设置数据源
     *
     * @param DataSource $dataSource
     * @return Model
     */
    public function useDataSource($dataSource) {
        $this->_ds = $dataSource;
        if (!empty ($this->_ds)) {
            $this->_ftable = $this->_ds->full_name($this->_table);
        }
        return $this;
    }

    // /////////////////////////////////////////////////////////////////////////////////////
    /**
     * 从数据库取出主键对应的数据
     *
     * @param null|int $id
     * @return boolean|array
     */
    public function read($id = null) {
        if (!is_numeric($id) && empty ($id)) {
            if (empty ($this->_whereString)) {
                $pk = $this->_pk [0];
                $data [$pk] = $this->{$pk};
                if (!is_null($data [$pk])) {
                    $this->where($data);
                }
            }
        } else if (is_array($id) && !empty ($id)) {
            $this->where($id);
        } else {
            $pk = $this->_pk [0];
            if ($this->{$pk} === $id) {
                return $this->data();
            }
            $data [$pk] = $id;
            if (!is_null($data [$pk])) {
                $this->where($data);
            }
        }
        $rst = $this->retrieve('*', 0);
        if (!empty ($rst)) {
            /** @var $rst array */
            $this->bind($rst);
            return $rst;
        }
        return false;
    }

    /**
     * 检索数据.
     *
     * @see retrieve()
     * @param mixed $fields
     *            检索的字段
     * @param int $index
     *            检索第几条数据
     * @return ResultSet 结果集.如果$index不为null，则返回值为array
     */
    public function select($fields = '*', $index = null) {
        return $this->retrieve($fields, $index);
    }

    /**
     * 得到第一条记录
     *
     * @param string $fields
     * @return array 第一条结果集
     */
    public function getOne($fields = '*') {
        return $this->retrieve($fields, 0);
    }

    /**
     * 保存模型对应的数据.
     *
     * $data 为key/value的映射的记录形势.可以是多条记录.
     * 如果$data的$key中包括主键,则修改记录,反之新增记录.如果$data为空,
     * 则使用本实例的属性对应的值.
     *
     * @param array $data
     *            要保存的数据.
     * @param array $where
     *            保存时进行数据检测
     * @return array 保存后的数据.
     */
    public function save($data = array(), $where = array()) {
        if (empty ($data)) {
            $data = $this->data();
        }

        if (!empty ($this->_whereString)) { // 有条件的保存,此时同update方法
            return $this->update($data);
        }

        if (!empty ($where) && $this->where($where)->exist()) { // 指定查询条件且记录存在时更新
            $this->where($where); // exist会清空查询条件
            return $this->update($data);
        }
        if (!empty ($this->_pk)) {
            $pk = $this->_pk [0];
            if ((!empty ($pk) && isset ($data [$pk]) && !empty ($data [$pk]))) {
                return $this->update($data, $data [$pk]);
            }
        }
        return $this->insert($data);
    }

    /**
     * 修改数据记录.
     *
     * $data 为key/value的映射的记录形势.
     * 如果$data为空,则使用本实例的属性对应的值.
     *
     * @param array $data
     *            要修改的记录
     * @param array $id
     *            要修改记录的ID或条件.
     * @return mixed 修改成功为原始数据,否则为false
     */
    public function update($data = null, $id = null) {
        $data = is_null($data) ? $this->data() : $data;
        if (!empty ($data)) {
            if (!is_null($id) && !is_array($id)) {
                $_data = $data;
                unset ($_data [$this->_pk [0]]);
                $update_sql = $this->update_sql($_data, $id);

            } else if (is_array($id)) {
                $this->where($id);
                $update_sql = $this->update_sql($data);

            } else {
                $update_sql = $this->update_sql($data);
            }

            $rst = empty ($update_sql) ? false : $this->execute($update_sql);

            if ($rst === false) {
                $data = false;
            }
        }
        return $data;
    }

    /**
     * 新增记录.
     *
     * $data 为key/value的映射的记录形势.可以是多条记录.
     * 如果$data为空,则使用本实例的属性对应的值.
     *
     * @param array $data
     * @return mixed 新增的数据.新增失败时返回false
     */
    public function insert($data = array()) {
        if (empty ($data)) {
            $data = $this->data();
        }
        $insert_sql = $this->insert_sql($data);
        if (!$insert_sql) {
            return false;
        }
        $rst = $this->execute($insert_sql);

        if ($rst > 0 && !empty ($this->_pk)) {
            $data [$this->_pk [0]] = $this->_ds->last_insert_id();
        } else if (empty ($rst)) {
            return false;
        }
        $this->bind($data);
        return $data;
    }

    /**
     * 删除符合条件的记录.
     *
     * 如果没有通过where指定条件,则优先使用主键,如果没有为主键属性赋值,则删除所有记录.
     *
     * @param array $where
     *            删除条件
     * @return int 影响的记录数，失败时返回false
     */
    public function delete($where = array()) {
        if (is_array($where) && !empty ($where)) {
            $this->where($where);
        }
        if (empty ($this->_whereString)) {
            if (!empty ($this->_pk)) {
                $pk = $this->_pk [0];
                if (!empty ($where)) {
                    $data [$pk] = $where;
                } else {
                    $data [$pk] = $this->{$pk};
                }
                if (!empty ($data [$pk])) {
                    $this->where($data);
                }
            }
        }
        $sql = "DELETE FROM `{$this->_ftable}` ";
        $this->_whereString = str_replace('`' . $this->_entity_name . '`.', '', $this->_whereString);
        $sql .= empty ($this->_whereString) ? '' : " WHERE {$this->_whereString}";
        return $this->execute($sql);
    }

    /**
     * 记录是否存在.
     *
     * 条件由WHERE输入
     *
     * @param array $where
     *            查询条件,默认为空
     * @return boolean
     */
    public function exist($where = null) {
        if (is_array($where) && !empty ($where)) {
            $this->where($where);
        }
        $_total = $this->count(false);
        return $_total > 0;
    }

    /**
     * 是否存在查询条件(EXISTS OR NOT EXISTS)
     *
     * @param string $sql
     *            用于判断是否存在的SQL语句
     * @param bool $not
     * @return Model
     */
    public function exists($sql, $not = false) {
        $this->_retrieveOptions ['exists'] = $not ? 'NOT EXISTS (' . $sql . ')' : 'EXISTS (' . $sql . ')';
        return $this;
    }

    /**
     * 最后一次生成的ID
     *
     * @return int
     */
    public function last_id() {
        return $this->_ds->last_insert_id();
    }

    /**
     * 符合条件的记录数
     *
     * @param bool $retrieve
     * @param string $field
     * @return Model
     */
    public function count($retrieve = false, $field = '*') {
        $this->_total = 0;
        $this->_countit = true;
        $this->_retrieveit = $retrieve;
        if ($field == '*' && empty ($this->_pk)) {
            $this->_countfield = "*";
        } else {
            $this->_countfield = $field == "*" ? "`{$this->_entity_name}`.`" . $this->_pk [0] . '`' : $this->sf($field);
        }
        if (!$retrieve) {
            return $this->retrieve();
        }
        return $this;
    }

    /**
     * 生成条件语句
     *
     * @param array $conditions
     * @return Model
     */
    public function where($conditions) {
        if (is_array($conditions) && !empty ($conditions)) {
            $this->_whereString = $this->where_sql($conditions);
        }
        return $this;
    }

    /**
     * 分页查询数据.
     *
     * @param int $limit
     *            分页大小,默认为null
     * @param int $start
     *            分页偏移,默认为null，使用当前分页信息
     * @return Model
     */
    public function limit($limit = null, $start = null) {
        if ($start == null && function_exists('paginginfo')) {
            $start = paginginfo() - 1;
        } else if (!is_numeric($start)) {
            $start = 0;
        }
        if (is_numeric($start) && is_numeric($limit)) {
            $this->_retrieveOptions ['limit'] = $limit;
            $this->_retrieveOptions ['start'] = $start;
        }
        return $this;
    }

    /**
     * 分组查询
     *
     * @param string $groupBy
     *            分组条件
     * @return Model
     */
    public function groupBy($groupBy) {
        $this->_retrieveOptions ['group'] = $groupBy;
        return $this;
    }

    /**
     * 排序
     *
     * @deprecated
     *
     *
     * @param string $orderBy
     *            排序语句
     * @return Model
     */
    public function orderBy($orderBy) {
        $this->_retrieveOptions ['order'] [] = $orderBy;
        return $this;
    }

    /**
     * 排序
     *
     * @param array $sort
     *            array("field","a|d")
     * @return Model
     */
    public function sort($sort) {
        if ($sort ['dir'] == 'a') {
            $this->sortAsc($sort ['field']);
        } else {
            $this->sortDesc($sort ['field']);
        }
        return $this;
    }

    /**
     * 按$field升序排列
     *
     * @param string $field
     * @return Model
     */
    public function sortAsc($field) {
        $this->_retrieveOptions ['order'] [] = $this->sf($field) . " ASC";
        return $this;
    }

    /**
     * 按$field降序排列
     *
     * @param string $field
     * @return Model
     */
    public function sortDesc($field) {
        $this->_retrieveOptions ['order'] [] = $this->sf($field) . " DESC";
        return $this;
    }

    /**
     * HAVING 子句
     *
     * @param string $having
     *            HAVING 子句
     * @return Model
     */
    public function having($having) {
        $this->_retrieveOptions ['having'] = $having;
        return $this;
    }

    /**
     * 连接查询
     *
     * @param $table
     * @param string $on
     *            连接条件
     * @param string $dir
     *            连接方向,默认为Model::LEFT左连接
     * @internal param \Model $entity 连接的实体*            连接的实体
     * @return Model
     */
    public function join($table, $on, $dir = Model::LEFT) {
        if ($table instanceof Model) {
            $j_tbl_name = $table->fullname;
            $j_entity = $table->entity;
        } else {
            $table = preg_split('/(as|\s+)/i', trim($table));
            if (count($table) >= 2) {
                $j_tbl_name = $this->_ds->full_name(array_shift($table));
                $j_entity = array_pop($table);
            } else {
                $table = $table [0];
                $j_tbl_name = $this->_ds->full_name($table);
                $j_entity = ucfirst($table);
            }
        }
        $sql = $dir . " JOIN `{$j_tbl_name}` AS `{$j_entity}` ON ({$on})";
        $this->_joins [] = $sql;
        return $this;
    }

    /**
     * 添加自定义查询字段，一般用于通过SQL查询出结果
     *
     * @param string $selectSql
     * @param string $alias
     * @return Model
     */
    public function subfield($selectSql, $alias = '') {
        if (!empty ($alias)) {
            $this->_cfields [] = '(' . $selectSql . ') AS `' . $alias . "`";
        } else {
            $this->_cfields [] = $selectSql;
        }
        return $this;
    }

    /**
     * 得到一条SQL
     *
     * @param string $fields
     * @return string SQL语句
     */
    public function getSql($fields = '*') {
        $fields = gettype($fields) == 'string' ? $fields : implode(',', array_unique($fields));
        $fields = $fields == "*" ? $this->_all : $fields;
        if (!empty ($this->_cfields)) {
            $fields .= "," . implode(",", $this->_cfields);
        }
        $tbl_name = $this->_ftable;
        $options = $this->_retrieveOptions;
        if (!empty ($options)) {
            extract($options);
            unset ($options);
        }
        $entities = "`{$tbl_name}` AS `{$this->_entity_name}`";
        if (!empty ($this->_joins)) {
            foreach ($this->_joins as $join) {
                $entities = "({$entities} {$join})";
            }
        }
        $condition = $this->_whereString;
        $sql = "SELECT {$fields} FROM $entities";
        $sql .= empty ($condition) ? '' : " WHERE {$condition}";
        if (isset ($exists)) {
            $sql .= empty ($condition) ? " WHERE {$exists}" : " AND {$exists}";
        }
        $sql .= (isset ($group) && !empty ($group)) ? " GROUP BY {$group}" : '';
        $sql .= (isset ($order) && !empty ($order)) ? " ORDER BY " . implode(',', $order) : ''; // 查总数时,不需要加上排序
        $sql .= (isset ($having) && !empty ($having)) ? " HAVING {$having}" : '';

        $this->clearOptions();
        return $sql;
    }

    /**
     * 检索数据.
     *
     * @param mixed $fields
     *            检索的字段
     * @param int $index
     *            检索第几条数据
     * @return ResultSet 结果集.
     */
    public function retrieve($fields = '*', $index = null) {

        $fields = gettype($fields) == 'string' ? $fields : implode(',', array_unique($fields));

        $fields = $fields == "*" ? $this->_all : $fields;
        if (!empty ($this->_cfields)) {
            $fields .= "," . implode(",", $this->_cfields);
        }
        $tbl_name = $this->_ftable;

        $options = $this->_retrieveOptions;
        if (!empty ($options)) {
            extract($options);
            unset ($options);
        }
        $entities = "`{$tbl_name}` AS `{$this->_entity_name}`";
        if (!empty ($this->_joins)) {
            foreach ($this->_joins as $join) {
                $entities = "({$entities} {$join})";
            }
        }
        $condition = $this->_whereString;

        $sql = "SELECT {$fields} FROM $entities";
        if ($this->_countit) {
            $sql__total = "SELECT COUNT(" . $this->_countfield . ") as _total FROM $entities";
        }
        $sql .= empty ($condition) ? '' : " WHERE {$condition}";
        if (isset ($exists)) {
            $sql .= empty ($condition) ? " WHERE {$exists}" : " AND {$exists}";
        }
        $sql__total = '';
        if ($this->_countit) {
            $sql__total .= empty ($condition) ? '' : " WHERE {$condition}";
            if (isset ($exists)) {
                $sql__total .= empty ($condition) ? " WHERE {$exists}" : " AND {$exists}";
            }
        }
        $sql .= (isset ($group) && !empty ($group)) ? " GROUP BY {$group}" : '';
        if ($this->_countit) {
            $sql__total .= (isset ($group) && !empty ($group)) ? " GROUP BY {$group}" : '';
        }
        $sql .= (isset ($order) && !empty ($order)) ? " ORDER BY " . implode(',', $order) : '';
        // 查总数时,不需要加上排序
        $sql .= (isset ($having) && !empty ($having)) ? " HAVING {$having}" : '';
        if ($this->_countit) {
            $sql__total .= (isset ($having) && !empty ($having)) ? " HAVING {$having}" : '';
        }
        if (isset ($limit) && is_int($limit)) {
            $start = isset ($start) && is_int($start) ? $start : 0;
            $options = array('start' => $start, 'limit' => $limit);
        } else {
            $options = array();
        }
        if ($this->_countit) {
            $_total = $this->query($sql__total);
            if (!empty ($_total)) {
                $this->_total = $_total->fieldAt("_total", 0, 0);
                $_total->dispose();
            }
            $this->_countit = false;
        }
        if ($this->_retrieveit) {
            $records = $this->query($sql, $options);
            if (is_numeric($index)) {
                if (!empty ($records) && $records->rowCount() > 0) {
                    $records->countTotal = $this->_total;
                    return $records->at($index);
                } else {
                    return array();
                }
            } else {
                if (!empty ($records)) {
                    $records->countTotal = $this->_total;
                }
                return $records;
            }
        } else {
            $this->_retrieveit = true;
            return $this->_total;
        }
    }

    /**
     * 得到一条记录中的一个字段值
     *
     * @param string $field
     * @param array $where
     * @return mixed false时未找到值
     */
    public function get($field, $where = array()) {
        if (!empty ($where)) {
            $this->where($where);
        }
        $rst = $this->retrieve($field, 0);
        if (empty ($rst)) {
            return false;
        } else {
            return $rst [$field];
        }
    }

    /**
     * 将结果集连接在一起
     *
     * @param string $field
     *            要连接在一起的字段
     * @param string $glue
     *            连接字符
     * @return string
     */
    public function field_join($field, $glue = ',') {
        $rst = $this->retrieve($field);
        $pieces = array();
        if ($rst) {
            foreach ($rst as $r) {
                $pieces [] = $r [$field];
            }
        }
        return join($glue, $pieces);
    }

    /**
     * 用结果集形成key/value形式的map
     *
     * @param string $key_field
     *            将作为Key值的字段
     * @param string $value_field
     *            作为value值的字段
     * @return array
     */
    public function field_map($key_field, $value_field) {
        $rst = $this->retrieve($key_field . ',' . $value_field);
        $ary = array();
        if ($rst) {
            foreach ($rst as $r) {
                $ary [$r [$key_field]] = $r [$value_field];
            }
        }
        return $ary;
    }

    /**
     * 执行一条SQL语句.
     *
     * @param string $sql
     *            标准的SQL,不包括分页信息.
     * @param array $options
     *            array('start'=>0,'limit'=>page_size)分页信息.
     * @return ResultSet
     */
    public function query($sql, $options = array()) {
        if (is_array($options) && isset ($options ['start']) && isset ($options ['limit'])) {
            $rst = $this->_ds->queryLimit($sql, $options ['start'], $options ['limit']);
        } else {
            $rst = $this->_ds->query($sql);
        }
        $this->clearOptions($sql);
        return $rst;
    }

    /**
     * 执行一条SQL语句,返回影响的记录条数.
     *
     * @param string $sql
     * @return int
     */
    public function execute($sql) {
        $this->clearOptions($sql);
        return $this->_ds->execute($sql);
    }

    // /////////////////////////////////////////////////////////////////////////////////////
    /**
     * 魔术方法获取字段
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if ($name == 'fullname') {
            return $this->_ftable;
        } else if ($name == 'entity') {
            return $this->_entity_name;
        } else if ($name == '_queryString') {
            return $this->_queryString;
        } else if($name == '_ds'){
            return $this->_ds;
        } else if (isset ($this->_fields [$name])) {
            $field = $this->_fields [$name] ['var'];
            return $this->{$field};
        } else if (isset ($this->{$name})) {
            return $this->{$name};
        }
        return null;
    }

    public function __set($name, $value) {
        // TODO 数据校验
        if (isset ($this->_fields [$name])) {
            $field = $this->_fields [$name] ['var'];
            $this->{$field} = $value;
        }
    }

    /**
     * 取得实体map格式的数据
     *
     * @return array Model实例map后的数据
     */
    public function data() {
        $datas = array();
        foreach ($this->_fields as $field => $info) {
            $datas [$field] = $this->{$info ['var']};
        }
        return $datas;
    }

    /**
     * 使用$map对数组进行映射
     *
     * @param array $data
     *            原数据
     * @param array $map
     *            映射规则
     * @param bool $uk
     * @return array 映射后的数据
     */
    public function map($data, $map, $uk = true) {
        $mdata = array();
        foreach ($map as $key => $mkey) {
            $key1 = $uk ? $key : $mkey;
            $key2 = $uk ? $mkey : $key;
            if (isset ($data [$key1])) {
                $mdata [$key2] = $data [$key1];
            }
        }
        return $mdata;
    }

    /**
     * 将map类型的数据绑定到当前Model
     *
     * @param array $data
     *            map格式,key/value
     */
    public function bind($data) {
        // TODO 进行类型校验
        foreach ($this->_fields as $field => $info) {
            if (isset ($data [$field])) {
                $this->{$info ['var']} = $data [$field];
            }
        }
    }

    /**
     * 最后错误信息
     *
     * @return string 最后错误信息
     */
    public function last_error_msg() {
        return $this->_ds->last_error_msg();
    }

    /**
     *
     *
     *
     * 安全字段.
     *
     * 将$field进行安全处理
     *
     * @param string $field
     * @return string
     */
    public function sf($field) {
        $fields = explode('.', $field);
        if (count($fields) == 1) {
            array_unshift($fields, $this->_entity_name);
        }
        return "`{$fields[0]}`.`{$fields[1]}`";
    }

    /**
     * 是否有字段$field
     *
     * @param string $field
     * @return boolean
     */
    public function isField($field) {
        return isset ($this->_fields [$field]);
    }

    /**
     *
     * 生成树结构数据
     *
     * @param string $upidf
     *            父结点字段
     * @param mixed $upid
     *            父结点值
     * @param string $idf
     *            结点标准字段
     * @param mixed $id
     *            结点值
     * @param mixed $filed
     *            读取的字段除upidf和idf,
     * @param boolean $skip
     *            是否跳过结点值与$id相同的数据
     * @param bool|string $prepare
     *            用户自定义数据处理函数
     * @param string $sub
     *            访问子结点的key
     * @return array
     */
    public function treeData($upidf, $upid, $idf, $id, $filed = '*', $skip = false, $prepare = false, $sub = 'items') {
        $trees = array();
        if ($filed != '*') {
            settype($filed, 'array');
            $rfield = $filed [0];
            $fileds = array_merge($filed, array($upidf, $idf));
        } else {
            $fileds = '*';
            $rfield = $idf;
        }
        $data = $this->where(array($upidf => $upid))->retrieve($fileds);
        if ($data !== false) {
            foreach ($data as $d) {
                $d = $prepare === false ? $d : call_user_func_array($prepare, array($d));
                if ($skip === true && $d [$idf] == $id) {
                    continue;
                }
                if ($sub == false) {
                    $trees [] = $d [$rfield];
                    $nodes = $this->treeData($upidf, $d [$idf], $idf, $id, $filed, $skip, $prepare, false);
                    $trees = array_merge($trees, $nodes);
                } else {
                    $d [$sub] = $this->treeData($upidf, $d [$idf], $idf, $id, $filed, $skip, $prepare, $sub);
                    $trees [] = $d;
                }
            }
        }
        return $trees;
    }

    /**
     * 面包屑
     *
     * @param string $upfield
     *            上级字段
     * @param int $id
     *            主键
     * @param string $name_field
     *            名字字段
     * @return array
     */
    public function crumb($upfield, $id, $name_field) {
        if (empty ($this->_pk [0])) {
            return false;
        }
        $crumb_arry = array();
        $args = func_get_args();
        if (count($args) > 3) {
            $args = array_slice($args, 3);
        } else {
            $args = array();
        }
        do {
            if (empty ($id)) {
                break;
            }
            $crumb = $this->read($id);
            if ($crumb === false || $crumb [$upfield] == $id) {
                break;
            }
            $cm = array();
            if ($args) {
                foreach ($args as $a) {
                    $cm [$a] = $crumb [$a];
                }
            }
            $cm ['name'] = $crumb [$name_field];
            $cm ['id'] = $id;
            array_unshift($crumb_arry, $cm);
            $id = $crumb [$upfield];
        } while (true);

        return $crumb_arry;
    }

    /**
     * 开启事务.
     *
     * 在一次事务中,只需要一个model开启即可
     *
     * @return boolean 成功返回true,失败返回false
     */
    public function begin() {
        return $this->_ds->begin();
    }

    /**
     *
     *
     *
     * 提交事务.
     *
     * 只需要一个model提交即可,通常由开启事务的那个model提交
     *
     * @return boolean 成功返回true,失败返回false
     */
    public function commit() {
        return $this->_ds->commit();
    }

    /**
     *
     *
     *
     * 回滚事务.
     *
     * 只要一个model回滚即可,通常由开启事务的那个model回滚
     *
     * @return boolean 成功返回true,失败返回false
     */
    public function rollback() {
        return $this->_ds->rollback();
    }

    /**
     *
     *
     *
     * 为实体类指定别名
     *
     * @param string $alias
     * @return Model
     */
    public function alias($alias) {
        if (!empty ($alias)) {
            $this->_old_entity_name = $this->_entity_name;
            $this->_entity_name = $alias;
        }
        return $this;
    }

    /**
     * 编译SQL的条件语句.
     *
     * @param array $conditions
     *            条件.
     * @return string 预处理的复合查询条件.
     * @access protected
     */
    protected function where_sql($conditions) {
        if (empty ($conditions) || !is_array($conditions)) {
            return '';
        }
        $c = array();
        $first = true;
        foreach ($conditions as $and_or => $condition) {
            $and_ors = preg_split('/[\s]+/', trim($and_or));
            $and_ors = count($and_ors) >= 2 ? $and_ors : array($and_ors [0], '=');
            $and_or = array_shift($and_ors); // 字段
            $op = strtoupper(join(" ", $and_ors)); // 运算符
            if (preg_match('/(.*?)([!=><]{1,2})/i', $and_or, $m)) {
                $and_or = $m [1];
                $op = $m [2];
            }
            $ofield = $and_or;

            if (is_array($condition) && !in_array($op, array('BETWEEN', 'IN', 'NOT IN'))) { // 如果是嵌套条件
                if (!is_numeric($and_or)) {
                    $_and_or = strtoupper($and_or);
                    if ($_and_or == 'AND' || $_and_or == "OR") {
                        $c [] = $_and_or;
                    }
                }
                $c [] = '(';
                $c [] = $this->where_sql($condition);
                $c [] = ')';
            } else {
                $and_or = $this->sf($and_or); // 安全字段
                $left_val = isset ($this->_fields [$and_or]) ? "`{$and_or}`" : $and_or;
                if (!$first) {
                    $c [] = 'AND';
                }
                if (is_null($condition)) {
                    $c [] = $left_val;
                    $c [] = 'IS NULL';
                } else if (NOTNULL === $condition) {
                    $c [] = $left_val;
                    $c [] = 'IS NOT NULL';
                } else {
                    $c [] = $left_val . ' ' . $op;
                    if ($op == 'IN' || $op == 'NOT IN') {
                        if (is_array($condition)) {
                            if (count($condition) > 1) {
                                $c [] = "(" . implode(",", $this->safeValue($ofield, $condition)) . ")";
                            } else if (count($condition) == 1) {
                                array_pop($c);
                                $c [] = $left_val . ($op == 'IN' ? ' = ' : ' <> ');
                                $c [] = $this->safeValue($ofield, $condition [0]);
                            } else {
                                array_pop($c);
                            }
                        } else {
                            $c [] = " ({$condition})";
                        }
                    } else if ($op == 'BETWEEN') {
                        if (is_array($condition)) {
                            $c [] = $this->safeValue($ofield, $condition [0]) . ' AND ' . $this->safeValue($ofield, $condition [1]);
                        } else {
                            $c [] = $condition;
                        }
                    } else {
                        $c [] = $this->safeValue($ofield, $condition);
                    }
                }
            }
            if ($first) {
                $first = false;
            }
        }
        $first = array_shift($c);
        $c = implode(' ', $c);
        if (!($first == 'AND' || $first == 'OR')) {
            $c = $first . ' ' . $c;
        }
        return $c;
    }

    /**
     * 对值进行安全处理.
     * 根据字段的类型，对用户传入的值进行安全检查,并返回安全的值。
     *
     * @param
     *            string ofield 字段名
     * @param mixed $value
     *            值
     * @return mixed 安全处理的值
     */
    public function safeValue($ofield, $value) {
        if (isset ($this->_fields [$ofield])) {
            $type = $this->_fields [$ofield] ['type'];
            if ('number' == $type && is_numeric($value)) {
                return $value;
            } else if (is_array($value)) {
                $vs = array();
                foreach ($value as $v) {
                    if ('number' == $type && is_numeric($v)) {
                        $vs [] = $v;
                    } else if ('number' != $type && strlen(trim($v, '`')) == strlen($v)) {
                        $vs [] = "'" . $this->_ds->escape($v) . "'";
                    } else {
                        $vs [] = $this->_ds->escape($v);
                    }
                }
                return $vs;
            }
        }
        if (strlen(trim($value, '`')) == strlen($value)) {
            return "'" . $this->_ds->escape($value) . "'";
        } else {
            return $this->_ds->escape($value);
        }
    }

    /**
     * 生成insert sql
     *
     * @param string $data
     *            数据
     * @return bool|string
     */
    protected function insert_sql($data) {
        $fields = array();
        $values = array();
        foreach ($this->_fields as $field => $meta) {
            if ($meta ['auto_increment'] === true) {
                continue;
            }
            if ($meta ['required'] && !isset ($data [$field]) && is_null($meta ['default']) && empty ($meta ['time_update'])) { // 必填字段
                $this->_ds->last_error_msg('The field ' . $field . ' of ' . $this->_entity_name . ' is required');
                return false;
            }
            $type = $meta ['type'];
            $value = isset ($data [$field]) ? $data [$field] : (is_null($meta ['default']) ? ($type == 'string' ? '' : 0) : $meta ['default']);

            $fields [] = '`' . $field . '`';

            if (empty ($value)) {
                if ($meta ['time_update'] == 't') {
                    $value = date('Y-m-d H:i:s');
                } else if ($meta ['time_update'] == 'T') {
                    $value = time();
                } else if ($meta ['time_update'] == 'd') {
                    $value = date('Y-m-d');
                }
            }
            $value = $this->_ds->escape($value);
            if ($type == 'number') {
                $values [] = "{$value}";
            } else {
                $values [] = "'{$value}'";
            }
        }
        $fs = join(',', $fields);
        $vs = join(',', $values);

        return "INSERT INTO `{$this->_ftable}` ({$fs}) VALUES ({$vs})";
    }

    /**
     * 生成update sql
     *
     * @param array $data
     *            数据
     * @param mixed $id
     *            主键
     * @return bool|string
     */
    protected function update_sql($data, $id = null) {
        $fields = array();
        if (!is_null($id)) {
            $this->where(array($this->_pk [0] => $id));
        }
        settype($data, 'array');
        $condition = $this->_whereString;
        foreach ($this->_fields as $field => $meta) {
            if (isset ($data [$field])) {
                $value = $this->_ds->escape($data [$field]);
                if ($meta ['type'] == 'number') {
                    $fields [] = !is_null($value) ? "`{$this->_entity_name}`.`" . $field . "` = " . $value : "`{$this->_entity_name}`.`" . $field . "` =  NULL";
                } else {
                    $fields [] = !is_null($value) ? "`{$this->_entity_name}`.`" . $field . "` = '" . $value . "'" : "`{$this->_entity_name}`.`" . $field . "` =  NULL";
                }
            } else if (isset ($data ["&$field"])) {
                $value = $data ["&$field"];
                $fields [] = !is_null($value) ? "`{$this->_entity_name}`.`" . $field . "` = " . $value : "`{$this->_entity_name}`.`" . $field . "` =  NULL";
            } else if (!empty ($meta ['time_update']) && isset ($meta ['auto_update'])) {
                $value = $meta ['time_update'] == 't' ? "'" . date('Y-m-d H:i:s') . "'" : ($meta ['time_update'] == 'T' ? time() : "'" . date('Y-m-d') . "'");
                $fields [] = "`{$this->_entity_name}`.`{$field}` = {$value}";
            }
        }
        if (!empty ($fields)) {
            $fs = join(',', $fields);
            return "UPDATE `{$this->_ftable}` AS `{$this->_entity_name}` SET {$fs} " . (empty ($condition) ? '' : " WHERE {$condition}");
        } else {
            $this->_ds->last_error_msg('No fields of ' . $this->_entity_name . ' were to be updated');
            return false;
        }
    }

    /**
     * 生成表描述
     *
     * 根据实体类提供的字段定义,生成表描述信息.
     */
    private function schema() {
        $fields = get_object_vars($this);

        foreach ($fields as $field => $val) {
            if (!preg_match('/^[a-z][a-z_]*(_[adtnsrk]{0,5})?/i', $field)) {
                continue;
            }
            $info = explode('_', $field);
            $option = array_pop($info);
            if (empty ($info) || !preg_match('/[adtnsrk]{1,5}/i', $option)) { // 使用默认选项's'
                $info [] = $option;
                $option = 's';
            }

            $fid = join('_', $info);

            $finfo = array('field' => $fid, 'type' => 'string', 'var' => $field, 'default' => $val, 'required' => false, 'auto_increment' => false, 'time_update' => false);

            $len = !empty ($option) ? strlen($option) : 0;

            for ($i = 0; $i < $len; $i++) {
                $op = $option{$i};
                switch ($op) {
                    case 'n' : // 数值型,int float,long,number等
                        $finfo ['type'] = 'number';
                        break;
                    case 's' : // 类型:字符型
                        $finfo ['type'] = 'string';
                        break;
                    case 'r' : // 必须有值
                        $finfo ['required'] = true;
                        break;
                    case 'k' : // 主键
                        $this->_pk [] = $fid;
                        break;
                    case 'a' : // 自增字段
                        $finfo ['auto_increment'] = true;
                        break;
                    case 't' : // 日期时间:2010-09-09 15:00:00
                        $finfo ['time_update'] = 't';
                        break;
                    case 'T' : // 日期加时间的INT型
                        $finfo ['time_update'] = 'T';
                        $finfo ['type'] = 'number';
                        break;
                    case 'd' : // 日期:2010-09-09
                        $finfo ['time_update'] = 'd';
                        break;
                    case 'u' : // 自动更新日期和时间字段
                        $finfo ['auto_update'] = true;
                        break;
                    default :
                }
            }
            $this->_fields [$fid] = $finfo;
        }
        $keys = array_keys($this->_fields);
        foreach ($keys as $key => $f) {
            $keys [$key] = '`' . $this->_entity_name . '`.`' . $f . '`';
        }
        $this->_all = implode(',', $keys);
    }

    private function clearOptions($sql = '') {
        $this->_joins = array();
        $this->_retrieveOptions = array();
        $this->_whereString = '';
        $this->_queryString = $sql;
        $this->_duration = 0;
        $this->_entity_name = $this->_old_entity_name;
        $this->_cfields = array();
    }
}
// END OF FILE model.php