<?php
/**
 * 角色表单
 * @author Leo
 *
 */
class RoleForm extends BootstrapForm {
    var $rid = array (FWT_WIDGET => 'hidden', FWT_INITIAL => 0 );
    var $label = array (FWT_LABEL => '角色标识', FWT_TIP => '唯一的角色标识字符,只能由字母,数字或下划线组成.', FWT_VALIDATOR => array ('required' => '角色标识不能为空', 'regexp(/^[a-z_][a-z0-9_]*$/i)' => '只能由字母,数字或下划线组成', 'callback(@check_role_name,rid)' => '角色已经存在.' ) );
    var $name = array (FWT_LABEL => '角色名', FWT_TIP => '角色名,可重复但不建议,不能含有特殊字符,可以使用中文.', FWT_VALIDATOR => array ('required' => '角色名不能为空,', 'regexp(/[^\'\*\.]/)' => '不能含有特殊字符' ) );
    var $note = array (FWT_LABEL => '备注', FWT_WIDGET => 'textarea' );
    protected function getDefaultWidgetOptions() {
        return array (FWT_OPTIONS => array ('class' => 'span4' ), FWT_TIP_SHOW => FWT_TIP_SHOW_S );
    }
}