<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo.core
 *
 * $Id$
 */
/**
 * 
 * Bootstrap 样式表格
 * @author Leo Ning
 *
 */
class BootstrapGrid extends SimpleGrid {
	public function __construct($data, $extraData = array(), $properties = array()) {
		parent::__construct ( $data, $extraData, $properties );
		$this->_properties ['class'] = 'table table-hover ' . $this->_properties ['class'];
	}
}

