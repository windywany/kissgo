<?php
/**
 * 设置主题中页面类型模板时使用
 * @author Leo
 *
 */
class SetTplForm extends DataForm {
    var $theme = array (FWT_VALIDATOR => array ('required' => '请指定主题', 'maxlength(16)' => '主题最大长度为16.' ) );
    var $type = array (FWT_VALIDATOR => array ('required' => '请指定页面类型' ) );
    var $template = array (FWT_VALIDATOR => array ('required' => '请指定模板', 'regexp(/.+\\.tpl$/)' => '模板扩展名必须是tpl' ) );
}