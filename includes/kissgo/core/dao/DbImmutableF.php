<?php
/**
 * 不可变的字段
 * @author Leo Ning
 *
 */
class DbImmutableF implements DbImmutable {
    private $field;
    private $char;
    private $alias;
    public function __construct($field, $alias = null) {
        $this->field = $field;
        $this->alias = $alias;
        $this->char = '`';
    }
    public function setSpecialChar($char) {
        $this->char = $char;
    }
    public function __toString() {
        $str = $this->field;
        $func = '';
        $func1 = '';
        $table = '';
        if (preg_match ( '#((.*)\s*\()([^\.]+)(\.(.+))?(\))#', $str, $m )) { // count(a[.id])
            $func = $m [1];
            if (! empty ( $m [5] )) {
                $table = $this->char . trim ( $m [3] ) . $this->char . '.';
                $field = trim ( $m [5] );
            } else {
                $field = trim ( $m [3] );
            }
            $func1 = $m [6];
        } else if (preg_match ( '#([^\.]+)(\.(.+))?#', $str, $m )) { // a[.id]            
            $len = count ( $m );
            if ($len == 4) {
                $table = $this->char . trim ( $m [1] ) . $this->char . '.';
                $field = trim ( $m [3] );
            } else {
                $field = trim ( $m [1] );
            }
        } else {
            $field = $str;
        }
        
        if ($field == '*') {
            $str = $func . $table . $field . $func1;
        } else {
            $str = $func . $table . $this->char . $field . $this->char . $func1;
        }
        
        if ($this->alias) {
            $str .= ' AS ' . $this->char . $this->alias . $this->char;
        }
        return $str;
    }
}