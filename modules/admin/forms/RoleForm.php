<?php
/**
 * 角色表单
 * @author Leo
 *
 */
class RoleForm extends BootstrapForm {
    var $rid = array (FWT_WIDGET => 'hidden' );
    var $label = array (FWT_LABEL => '角色标识', FWT_TIP => '唯一的角色标识字符,只能由字母,数字或下划线组成.', FWT_VALIDATOR => array ('required' => '角色标识不能为空', 'regexp(/^[a-z0-9_]+$/i)' => '只能由字母,数字或下划线组成', 'callback(@check_role)' => '角色已经存在.' ) );
    var $name = array (FWT_LABEL => '角色名', FWT_TIP => '角色名,可重复胆不建议.', FWT_VALIDATOR => array ('required' => '角色名不能为空' ) );
    var $note = array (FWT_LABEL => '备注', FWT_WIDGET => 'textarea', FWT_OPTIONS => array ('class' => 'span4' ) );
    public function check_role($value, $data, $message) {
        imports ( 'admin/forms/RoleForm.php' );
        return $message;
    }
}