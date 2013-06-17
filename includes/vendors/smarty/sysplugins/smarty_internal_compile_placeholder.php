<?php
/*
 * Smarty Internal Plugin Compile Placeholder from Placeholder
 *
 * Compiles the {placeholder} {/placeholder} tags
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Leo Ning
 */

/**
 * Smarty Internal Plugin Compile Placeholder Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Placeholder extends Smarty_Internal_CompileBase {
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array ('name' );
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array ('_any' );
    
    /**
     * Compiles code for the {placeholder} tag
     *
     * @param array  $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter) {
        $tpl = $compiler->template;
        // check and get attributes
        $_attr = $this->getAttributes ( $compiler, $args );
        $name = trim ( $_attr ['name'], '\'"' );
        $this->openTag ( $compiler, 'placeholder', array ('placeholder', $compiler->nocache, $name, $name ) );
        $output = "<!--placeholder:{$name}-->";
        return $output;
    }
}
/**
 * Smarty Internal Plugin Compile placeholderclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Placeholderclose extends Smarty_Internal_CompileBase {
    
    /**
     * Compiles code for the {/placeholder} tag
     *
     * @param array  $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter) {
        // check and get attributes
        $_attr = $this->getAttributes ( $compiler, $args );
        // must endblock be nocache?
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }
        list ( $openTag, $compiler->nocache, $name, $key ) = $this->closeTag ( $compiler, array ('placeholder' ) );
        return "<!--/placeholder:{$name}-->";
    }
}
