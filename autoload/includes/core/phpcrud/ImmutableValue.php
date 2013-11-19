<?php
/**
 * Immutable value for a value which references to field or a function
 * 
 * @author guangfeng.ning
 *
 */
class ImmutableValue {
    private $value;
    public function __construct($value) {
        $this->value = $value;
    }
    public function __toString() {
        return $this->value;
    }
}