<?php
class DbImmutableV implements DbImmutable {
    private $value;
    public function __construct($value) {
        $this->value = $value;
    }
    public function setSpecialChar($char) {}
    public function __toString() {
        return $this->value;
    }
}