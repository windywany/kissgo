<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo.core
 *
 * $Id$
 */
define('BST_TEXT', 'text');
define('BST_FIELD', 'field');
define('BST_ORDER', 'order');
define('BST_SORT', 'sortable');
define('BST_RENDER', 'render');
/**
 * Simple Code:
 * <code>
 * class UserTable extends SimpleTable {
 *  var $name = array('text' => 'First Name', 'sortable' => true, 'render' => array('render_name'),'order'=>'d','field'=>'abc');
 *  var $name1 = array('text' => 'Last Name', 'sortable' => true, 'render' => array('render_name'));
 * }
 * </code>
 */
abstract class BaseTable {
    public function __construct($data, $extraData = array(), $properties = array()) {
        $this->initialize();
        $this->_extraData = $extraData;
        $this->_data = $data;
        $this->_properties = $properties;
    }

    public final function render() {
        return $this->drawHeader() . $this->drawBody() . $this->drawFooter();
    }

    private function initialize() {
        $defines = get_object_vars($this);
        $columns = array();
        foreach ($defines as $name => $column) {
            if (!isset($column['field']) || empty($column['field'])) {
                $column['field'] = $name;
            }
            if (!isset($column['text'])) {
                $column['text'] = ucfirst($name);
            }
            $column['text'] = __($column['text']);
            if (isset($column['sortable']) && !isset($column['order'])) {
                $column['order'] = 'd';
            }
            $columns[$name] = $column;
        }
        $this->_columns = $columns;
    }

    protected function getCellComponent($cell, $row) {
        return $cell['value'];
    }

    protected function getHeadCellComponent($cell) {
        return $cell['text'];
    }

    protected function getColumns() {
        return $this->_columns;
    }

    protected function getColumnsCount() {
        return count($this->_columns);
    }

    protected function getExtraData($name) {
        if (isset($this->_extraData[$name])) {
            return $this->_extraData[$name];
        }
        return array();
    }

    protected function getId() {
        return get_class($this);
    }

    protected function getData() {
        return $this->_data;
    }

    protected function render_checkbox($cell) {
        return '<input type="checkbox" class="chk" name="' . $cell['field'] . '[]" value="' . $cell['value'] . '"/>';
    }

    protected abstract function drawHeader();

    protected abstract function drawBody();

    protected abstract function drawFooter();
}

/**
 * 基本表格
 */
class SimpleTable extends BaseTable {
    protected function drawHeader() {
        $head[] = '<table id="' . $this->getId() . '"><thead><tr>';
        foreach ($this->_columns as $column) {
            $head[] = '<th>' . $this->getHeadCellComponent($column) . '</th>';
        }
        $head[] = '</tr></thead>';
        return implode('', $head);
    }

    protected function drawBody() {
        $body = array('<tbody>');
        $data = $this->getData();
        if (empty($data)) {
            $body[] = '<tr><td colspan="' . $this->getColumnsCount() . '">无记录</td></tr>';
        } else {
            foreach ($data as $row) {
                $body[] = '<tr>';
                foreach ($this->_columns as $column) {
                    $column['value'] = $row[$column['field']];
                    $body[] = '<td>' . $this->getCellComponent($column, $row) . '</td>';
                }
                $body[] = '</tr>';
            }
        }
        $body[] = '</tbody></table>';
        return implode('', $body);
    }

    protected function drawFooter() {
        return '';
    }
}