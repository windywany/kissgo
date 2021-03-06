<?php
/**
 * 表结构定义
 * @author Leo
 *
 */
class DbSchema implements IteratorAggregate, ArrayAccess {
    protected static $DATE_TYPES = array (Idao::TYPE_DATETIME, Idao::TYPE_DATE, Idao::TYPE_TIMESTAMP );
    protected $description = '';
    protected $fields = array ();
    protected $indexes = array ();
    protected $primarykey = array ();
    public function __construct($description = '') {
        $this->description = $description;
    }
    public function getIndexes() {
        return $this->indexes;
    }
    public function getPrimaryKey() {
        return $this->primarykey;
    }
    public function getSerialPk() {
        if (count ( $this->primarykey ) == 1) {
            $def = $this->fields [$this->primarykey [0]];
            if ($def ['type'] == Idao::TYPE_SERIAL) {
                return $this->primarykey [0];
            }
        }
        return false;
    }
    public function addIndex($name, $fields) {
        if (! is_array ( $fields )) {
            $fields = array ($fields );
        }
        $this->indexes [] = array ($name, $fields, '' );
    }
    public function addUnique($name, $fields) {
        if (! is_array ( $fields )) {
            $fields = array ($fields );
        }
        $this->indexes [] = array ($name, $fields, 'UNIQUE' );
    }
    public function addPrimarykey($fields) {
        if (is_array ( $fields )) {
            $this->primarykey += $fields;
        } else {
            $this->primarykey [] = $fields;
        }
    }
    public function getFields() {
        return $this->fields;
    }
    public function getAutoUpdateData(&$data, $alias = '', $char = '`') {
        $I = whoami ();
        $fields = array_merge ( array (), $this->fields );
        foreach ( $data as $name => $field ) {
            $name = str_replace ( array ($char, $alias, '.' ), '', $name );
            unset ( $fields [$name] );
        }
        $alias = $alias ? $alias . '.' : '';
        if (! empty ( $fields )) {
            foreach ( $fields as $f => $def ) {
                $f = $alias . $f;
                if (in_array ( Idao::AUTOUPDATE_DATE, $def, true ) && in_array ( $def [Idao::TYPE], self::$DATE_TYPES )) {
                    switch ($def [Idao::TYPE]) {
                        case Idao::TYPE_DATE :
                            $data [$f] = date ( 'Y-m-d' );
                            break;
                        case Idao::TYPE_DATETIME :
                            $data [$f] = date ( 'Y-m-d H:i:s' );
                            break;
                        case Idao::TYPE_TIMESTAMP :
                            $data [$f] = time ();
                            break;
                        default :
                            break;
                    }
                } else if (in_array ( Idao::AUTOUPDATE_UID, $def, true ) && $def [Idao::TYPE] == Idao::TYPE_INT) {
                    $data [$f] = $I ['uid'];
                }
            }
        }
    }
    public function getAutoInsertData(&$data) {
        $I = whoami ();
        $fields = array_merge ( array (), $this->fields );
        foreach ( $data as $name => $field ) {
            unset ( $fields [$name] );
        }
        if (! empty ( $fields )) {
            foreach ( $fields as $f => $def ) {
                if (in_array ( Idao::AUTOINSERT_DATE, $def, true ) && in_array ( $def [Idao::TYPE], self::$DATE_TYPES )) {
                    switch ($def [Idao::TYPE]) {
                        case Idao::TYPE_DATE :
                            $data [$f] = date ( 'Y-m-d' );
                            break;
                        case Idao::TYPE_DATETIME :
                            $data [$f] = date ( 'Y-m-d H:i:s' );
                            break;
                        case Idao::TYPE_TIMESTAMP :
                            $data [$f] = time ();
                            break;
                        default :
                            break;
                    }
                } else if (in_array ( Idao::AUTOINSERT_UID, $def, true ) && $def [Idao::TYPE] == Idao::TYPE_INT) {
                    $data [$f] = $I ['uid'];
                } else if (isset ( $def [Idao::DEFT] )) {
                    $data [$f] = $def [Idao::DEFT];
                }
            }
        }
    }
    public function getDescription() {
        return $this->description;
    }
    public function getIterator() {
        return new ArrayIterator ( $this->fields );
    }
    public function offsetExists($offset) {
        return isset ( $this->fields [$offset] );
    }
    public function offsetGet($offset) {
        return $this->fields [$offset];
    }
    public function offsetSet($offset, $value) {
        $this->fields [$offset] = $value;
    }
    public function offsetUnset($offset) {}
}