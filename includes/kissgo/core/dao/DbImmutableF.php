<?php
class DbImmutableF implements DbImmutable {
    private $field;
    private $char;
    private $alias;
    public function __construct($field, $alias = null) {
        $this->field = $field;
        $this->alias = $alias;
    }
    public function setSpecialChar($char) {
        $this->char = $char;
    }
    public function __toString() {
        $str = $this->field;
        if ($this->alias) {
            $str .= ' AS ' . $this->char . $this->alias . $this->char;
        }
        return $str;
    }
}