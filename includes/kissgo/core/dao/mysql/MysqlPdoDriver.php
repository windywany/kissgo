<?php
/**
 * 
 * Mysql Pdo Driver
 * @author Leo Ning
 *
 */
class MysqlPdoDriver extends PdoDriver implements SqlBuilder {
    /**
     * (non-PHPdoc)
     * @see PdoDriver::getSqlBuilder()
     * @return SqlBuilder
     */
    public function getSqlBuilder() {
        return $this;
    }
    /* (non-PHPdoc)
     * @see PdoDriver::buildOptions()
     */
    public function buildOptions($options) {
        $opts = array_merge ( array ('encoding' => 'UTF8', 'host' => 'localhost', 'port' => 3306, 'user' => 'root', 'password' => 'root', 'driver_options' => array () ), $options );
        $charset = isset ( $opts ['encoding'] ) && ! empty ( $opts ['encoding'] ) ? $opts ['encoding'] : 'UTF8';
        $dsn = "mysql:dbname={$opts['dbname']};host={$opts['host']};port={$opts['port']};charset={$charset}";
        return array ($dsn, $opts ['user'], $opts ['password'], $opts ['driver_options'] );
    }
    public function specialChar() {
        return '`';
    }
    /**
     * (non-PHPdoc)
     * @see SqlBuilder::schema()
     * @param Idao $dao
     * @return string
     */
    public function schema($dao, $engine = 'InnoDB', $charset = 'UTF8') {
        $sql = 'CREATE TABLE ';
        $schema = $dao->schema ();
        $sql .= '`' . $dao->getFullTableName () . '` (';
        $fields = array ();
        foreach ( $schema as $field => $def ) {
            $fstr = $this->getColumnDef ( $field, $def );
            if (! $fstr) {
                return '';
            }
            $fields [] = $fstr;
        }
        if (empty ( $fields )) {
            return '';
        }
        
        $primarykes = $schema->getPrimaryKey ();
        if (! empty ( $primarykes )) {
            $fields [] = "PRIMARY KEY (`" . implode ( '`,`', $primarykes ) . '`)';
        }
        
        $indexs = $schema->getIndexes ();
        foreach ( $indexs as $def ) {
            $fields [] = $this->getIndexDef ( $def );
        }
        
        $sql .= implode ( ',', $fields );
        
        $cmt = $schema->getDescription ();
        $sql .= ")ENGINE=$engine DEFAULT CHARSET $charset COMMENT '$cmt'";
        return $sql;
    }
    /**
     * (non-PHPdoc)
     * @see SqlBuilder::delete()
     */
    public function delete($table, $sqlHelper) {
        $values = new DbSqlValues ( $this );
        list ( $dao, $alias ) = $table;
        $fname = $dao->getFullTableName ();
        $sql = "DELETE `{$alias}` FROM `{$fname}` AS `{$alias}`";
        $join = $sqlHelper->getJoins ();
        if (! empty ( $join )) {
            $sql .= $this->buildJoin ( $join );
        }
        
        $condition = $sqlHelper->getCondition ();
        if (! empty ( $condition )) {
            $wsql = $this->buildWhere ( $condition, $values );
            if ($wsql) {
                $sql .= ' WHERE ' . $wsql;
            }
        }
        $order = $sqlHelper->getOrder ();
        if (! empty ( $order )) {
            $sql .= $this->buildOrder ( $order );
        }
        $limit = $sqlHelper->getLimit ();
        if (! empty ( $limit )) {
            $sql .= $this->buildLimit ( $limit, $values );
        }
        $data = $values->getValues ();
        return new DbSQL ( $sql, $data );
    }
    /**
     * (non-PHPdoc)
     * @see SqlBuilder::insert()
     */
    public function insert($table, $data) {
        list ( $dao, $alias ) = $table;
        $values = new DbSqlValues ( $this );
        $fname = $dao->getFullTableName ();
        $sql = "INSERT INTO `$fname` (`";
        $fields = $_values = array ();
        foreach ( $data as $field => $value ) {
            $fields [] = $field;
            $_values [] = $values->addValue ( $field, $value );
        }
        $sql .= implode ( "`,`", $fields ) . '`) VALUES (' . implode ( ',', $_values ) . ')';
        $data = $values->getValues ();
        return new DbSQL ( $sql, $data );
    }
    /**
     * (non-PHPdoc)
     * @see SqlBuilder::select()
     * @param DbSqlHelper $sqlHelper
     */
    public function select($from, $sqlHelper) {
        list ( $dao, $alias ) = $from;
        $values = new DbSqlValues ( $this );
        if ($dao instanceof DbSQL) {
            $values->merge ( $dao->values () );
            $fields = DbView::prepareFields ( $sqlHelper->getFields (), $values, '`' );
            $sql = "SELECT $fields FROM ( $dao ) AS `$alias`";
        } else {
            $fields = DbView::prepareFields ( $sqlHelper->getFields (), $values, '`', $dao );
            if (! $fields) {
                throw new PDOException ( "field lists is empty" );
            }
            $fname = $dao->getFullTableName ();
            $sql = "SELECT $fields FROM `$fname` AS `$alias`";
        }
        
        $join = $sqlHelper->getJoins ();
        if (! empty ( $join )) {
            $sql .= $this->buildJoin ( $join );
        }
        $condition = $sqlHelper->getCondition ();
        
        if (! empty ( $condition )) {
            $wsql = $this->buildWhere ( $condition, $values );
            if ($wsql) {
                $sql .= ' WHERE ' . $wsql;
            }
        }
        $group = $sqlHelper->getGroup ();
        if (! empty ( $group )) {
            $sql .= $this->buildGroupBy ( $group );
        }
        $having = $sqlHelper->getHaving ();
        if (! empty ( $having )) {
            $wsql = $this->buildWhere ( $having, $values );
            if ($wsql) {
                $sql .= ' HAVING ' . $wsql;
            }
        }
        $order = $sqlHelper->getOrder ();
        if (! empty ( $order )) {
            $sql .= $this->buildOrder ( $order );
        }
        $limit = $sqlHelper->getLimit ();
        if (! empty ( $limit )) {
            $sql .= $this->buildLimit ( $limit, $values );
        }
        $data = $values->getValues ();
        $dbSql = new DbSQL ( $sql, $data );
        return $dbSql;
    }
    /**
     * (non-PHPdoc)
     * @see SqlBuilder::update()
     */
    public function update($table, $data, $sqlHelper) {
        list ( $dao, $alias ) = $table;
        $values = new DbSqlValues ( $this );
        $fname = $dao->getFullTableName ();
        $sql = "UPDATE `$fname` AS `$alias`";
        $join = $sqlHelper->getJoins ();
        if (! empty ( $join )) {
            $sql .= $this->buildJoin ( $join );
        }
        $fields = array ();
        foreach ( $data as $field => $value ) {
            list ( $name, $vname ) = PdoDriver::safeField ( $field, '`' );
            if ($value instanceof DbImmutable) {
                $fields [] = "$name = " . $value->__toString ();
            } else {
                $pname = $values->addValue ( $vname, $value );
                $fields [] = "$name = " . $pname;
            }
        }
        if (empty ( $fields )) {
            return null;
        }
        $sql .= " SET " . implode ( ',', $fields );
        $condition = $sqlHelper->getCondition ();
        
        if (! empty ( $condition )) {
            $wsql = $this->buildWhere ( $condition, $values );
            if ($wsql) {
                $sql .= ' WHERE ' . $wsql;
            }
        }
        $order = $sqlHelper->getOrder ();
        if (! empty ( $order )) {
            $sql .= $this->buildOrder ( $order );
        }
        $limit = $sqlHelper->getLimit ();
        if (! empty ( $limit )) {
            $sql .= $this->buildLimit ( $limit, $values );
        }
        $data = $values->getValues ();
        $dbSql = new DbSQL ( $sql, $data );
        return $dbSql;
    }
    /**
     * 
     * Enter description here ...
     * @param unknown_type $field
     * @param unknown_type $definition
     */
    private function getColumnDef($field, $definition) {
        $fstr [] = "`{$field}`";
        $type = $definition [Idao::TYPE];
        $typee = isset ( $definition [Idao::TYPE_EXTRA] ) ? $definition [Idao::TYPE_EXTRA] : Idao::TE_NORMAL;
        if ($type == Idao::TYPE_SERIAL) {
            $definition ['auto_increment'] = true;
        } else if ($type == Idao::TYPE_BOOL) {
            $definition [Idao::LENGTH] = 1;
            if (isset ( $definition [Idao::DEFT] )) {
                $definition [Idao::DEFT] = $definition [Idao::DEFT] ? 1 : 0;
            }
        } else if ($type == Idao::TYPE_TIMESTAMP) {
            $definition [] = Idao::UNSIGNED;
        }
        $f = $this->getType ( $type, $typee );
        if (! $f) {
            return false;
        }
        if ($f == 'ENUM') {
            if (! isset ( $definition [Idao::ENUM_VALUES] ) || empty ( $definition [Idao::ENUM_VALUES] )) {
                return false;
            }
            $values = explode ( ',', $definition [Idao::ENUM_VALUES] );
            array_walk ( $values, array ($this, 'sanitize' ) );
            $fstr [] = $f . '(' . implode ( ',', $values ) . ')';
        } else if (isset ( $definition [Idao::LENGTH] )) {
            $fstr [] = $f . '(' . $definition [Idao::LENGTH] . ')';
        } else {
            $fstr [] = $f;
        }
        if (in_array ( Idao::UNSIGNED, $definition )) {
            $fstr [] = 'UNSIGNED';
        }
        if (in_array ( Idao::NN, $definition )) {
            $fstr [] = 'NOT NULL';
        } else {
            $fstr [] = 'NULL';
        }
        if (isset ( $definition [Idao::DEFT] )) {
            $fstr [] = 'DEFAULT ' . ((in_array ( $f, array ('VARCHAR', 'CHAR', 'TINYTEXT', 'MEDIUMTEXT', 'LONGTEXT', 'TEXT', 'DATE', 'DATETIME', 'ENUM' ) )) ? "'{$definition[Idao::DEFT]}'" : $definition [Idao::DEFT]);
        }
        if (isset ( $definition ['auto_increment'] )) {
            $fstr [] = 'AUTO_INCREMENT';
        }
        if (isset ( $definition [Idao::CMMT] )) {
            $fstr [] = "COMMENT '{$definition[Idao::CMMT]}'";
        }
        
        return implode ( ' ', $fstr );
    }
    private function getIndexDef($def) {
        list ( $name, $fields, $type ) = $def;
        if (! empty ( $type )) {
            return $type . ' KEY `' . $name . '` (`' . implode ( '`,`', $fields ) . '`)';
        } else {
            return 'KEY `' . $name . '` (`' . implode ( '`,`', $fields ) . '`)';
        }
    }
    /**
     * 生成join语句
     * @param array $joins
     */
    private function buildJoin($joins) {
        $_joins = array ();
        if (! empty ( $joins )) {
            foreach ( $joins as $join ) {
                list ( $table, $on, $dir, $alias ) = $join;
                if ($table instanceof DbView) {
                    $fname = $table->getFullTableName ();
                    $falias = $table->getAlias ();
                } else {
                    $fname = $this->getFullTableName ( $table );
                    $falias = $table;
                }
                if ($alias instanceof DbView) {
                    $falias = $alias->getAlias ();
                } else if (! empty ( $alias )) {
                    $falias = $alias;
                }
                $_joins [] = $dir . ' `' . $fname . '` AS `' . $falias . '` ON (' . $on . ')';
            }
            if (! empty ( $_joins )) {
                return implode ( ' ', $_joins );
            }
        }
        return '';
    }
    /**
     * 
     * build condition statement 
     * @param array $conditions
     * @param DbSqlValues $values
     */
    private function buildWhere($conditions, &$values) {
        if (empty ( $conditions ) || ! is_array ( $conditions )) {
            return '';
        }
        $c = array ();
        $first = true;
        foreach ( $conditions as $field => $condition ) {
            if (! is_numeric ( $field )) {
                if ($field {0} == '@') {
                    $eop = strtoupper ( substr ( $field, 1 ) );
                    switch ($eop) {
                        case 'OR' :
                            $c [] = 'OR';
                            $c [] = '(';
                            $c [] = $this->buildWhere ( $condition, $values );
                            $c [] = ')';
                            break;
                        case 'AND' :
                            $c [] = 'AND';
                            $c [] = '(';
                            $c [] = $this->buildWhere ( $condition, $values );
                            $c [] = ')';
                            break;
                        default :
                            if ($condition instanceof ResultCursor) {
                                $c [] = $eop . ' (' . $condition->__toString () . ')';
                                $_vs = $condition->getParams ();
                                if (is_array ( $_vs )) {
                                    $values->merge ( $_vs );
                                }
                            } else {
                                $c [] = $eop . ' ' . $condition;
                            }
                    }
                    continue;
                }
            } else if (is_string ( $condition )) {
                $field = $condition;
                $condition = '';
            }
            $and_ors = preg_split ( '/[\s]+/', trim ( $field ) );
            $and_ors = count ( $and_ors ) >= 2 ? $and_ors : array ($and_ors [0], '=' );
            $field = array_shift ( $and_ors ); // 字段
            $op = strtoupper ( implode ( " ", $and_ors ) ); // 运算符
            if (preg_match ( '/(.*?)([!=><]{1,2})/i', $field, $m )) {
                $field = $m [1];
                $op = $m [2];
            }
            if (! $first) {
                $c [] = 'AND';
            }
            if (is_array ( $condition ) && ! in_array ( $op, array ('BETWEEN', 'IN', 'NOT IN', 'IS NULL', 'IS NOT NULL' ) )) { // 如果是嵌套条件 
                if (! is_numeric ( $field )) {
                    continue;
                }
                $c [] = '(';
                $c [] = $this->buildWhere ( $condition, $values );
                $c [] = ')';
            } else {
                list ( $sfield, $field ) = PdoDriver::safeField ( $field, '`' );
                $c [] = $sfield;
                $c [] = $op;
                switch ($op) {
                    case 'BETWEEN' :
                        if (is_array ( $condition )) {
                            $v1 = $values->addValue ( $field, $condition [0] );
                            $v2 = $values->addValue ( $field, $condition [1] );
                            $c [] = $v1;
                            $c [] = 'AND';
                            $c [] = $v2;
                        } else {
                            $c [] = $condition;
                        }
                        break;
                    case 'IN' :
                    case 'NOT IN' :
                        $c [] = '(';
                        if (is_array ( $condition )) {
                            $vs = array ();
                            foreach ( $condition as $v ) {
                                $vs [] = $values->addValue ( $field, $v );
                            }
                            $c [] = implode ( ',', $vs );
                        } else {
                            $c [] = $condition;
                        }
                        $c [] = ')';
                        break;
                    case 'IS NULL' :
                    case 'IS NOT NULL' :
                        break;
                    default :
                        if ($condition instanceof DbImmutable) {
                            $c [] = $condition->__toString ();
                        } else {
                            $c [] = $values->addValue ( $field, $condition );
                        }
                }
            }
            if ($first) {
                $first = false;
            }
        }
        $first = array_shift ( $c );
        $c = implode ( ' ', $c );
        if (! ($first == 'AND' || $first == 'OR')) {
            $c = $first . ' ' . $c;
        }
        return $c;
    }
    // limit statement
    private function buildLimit($limits, &$values) {
        list ( $start, $limit ) = $limits;
        $start = $values->addValue ( 'start', ($start - 1) * $limit );
        $limit = $values->addValue ( 'limit', $limit );
        return " LIMIT $start,$limit ";
    }
    // group statement
    private function buildGroupBy($groupby) {
        $_gb = array ();
        foreach ( $groupby as $gb ) {
            $g = PdoDriver::safeField ( $gb, '`' );
            $_gb [] = $g [0];
        }
        return ' GROUP BY ' . implode ( ',', $_gb );
    }
    // order statement
    private function buildOrder($order) {
        $_or = array ();
        foreach ( $order as $o ) {
            list ( $field, $dir ) = $o;
            $fs = PdoDriver::safeField ( $field, '`' );
            $_or [] = $fs [0] . ' ' . $dir;
        }
        return ' ORDER BY ' . implode ( ',', $_or );
    }
    
    private function getType($type, $typee = 'normal') {
        static $map = array ('varchar:normal' => 'VARCHAR', 'char:normal' => 'CHAR', 'text:tiny' => 'TINYTEXT', 'text:small' => 'TINYTEXT', 'text:medium' => 'MEDIUMTEXT', 'text:big' => 'LONGTEXT', 'text:normal' => 'TEXT', 'serial:tiny' => 'TINYINT', 'serial:small' => 'SMALLINT', 'serial:medium' => 'MEDIUMINT', 'serial:big' => 'BIGINT', 'serial:normal' => 'INT', 'int:tiny' => 'TINYINT', 'int:small' => 'SMALLINT', 'int:medium' => 'MEDIUMINT', 'int:big' => 'BIGINT', 'int:normal' => 'INT', 
                'bool:normal' => 'TINYINT', 'float:tiny' => 'FLOAT', 'float:small' => 'FLOAT', 'float:medium' => 'FLOAT', 'float:big' => 'DOUBLE', 'float:normal' => 'FLOAT', 'numeric:normal' => 'DECIMAL', 'blob:big' => 'LONGBLOB', 'blob:normal' => 'BLOB', 'timestamp:normal' => 'INT', 'date:normal' => 'DATE', 'datetime:normal' => 'DATETIME', 'enum:normal' => 'ENUM' );
        $t = $type . ':' . $typee;
        if (isset ( $map [$t] )) {
            return $map [$t];
        } else if (isset ( $map [$type . ':normal'] )) {
            return $map [$type . ':normal'];
        }
        return false;
    }
    public function sanitize(&$items, $key) {
        $items = "'" . str_replace ( array ('\'', '"' ), '', trim ( $items ) ) . "'";
    }
}