<?php
class ModifyNodeTypeForm extends DataForm {
    var $id = array (FWT_VALIDATOR => array ('required' => '不能为空', 'number' => '必须是数字.' ) );
    var $tpl = array (FWT_VALIDATOR => array ('required' => '不能为空', 'regexp(/.+\\.tpl$/)' => '模板扩展名必须是tpl' ) );
}