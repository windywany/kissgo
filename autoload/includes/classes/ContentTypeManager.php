<?php

/**
 *
 * Content Type Manager
 * @author guangfeng.ning
 *
 */
class ContentTypeManager {
    private $types = array ();
    private $creatableTypes = array ();
    private static $INSTANCE;

    private function __construct() {
        apply_filter ( 'register_content_type', $this );
    }

    public static function getInstance() {
        if (! self::$INSTANCE) {
            self::$INSTANCE = new ContentTypeManager ();
        }
        return self::$INSTANCE;
    }

    /**
     * register the content type
     *
     * @param string $type the type id
     * @param string $name the type name
     * @param string $template the default template
     * @param string $note the note
     * @param boolean $creatable if the creatable
     */
    public function register($type, $name, $template, $note, $creatable = true) {
        $this->types [$type] = array ($name, $template, $creatable, $note );
        if ($creatable) {
            $this->creatableTypes [$type] = array ($name, $template, $note );
        }
    }

    public function getTypes() {
        return $this->types;
    }

    public function getCreatableTypes() {
        return $this->creatableTypes;
    }
}