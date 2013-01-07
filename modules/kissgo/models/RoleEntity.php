<?php
/*
 * 角色实体
 */
class RoleEntity extends Model {
    var $id_nak; //INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '组编号，主键',
    var $label_r; //VARCHAR(45) NOT NULL COMMENT '用户组标识',
    var $name_r; //VARCHAR(45) NOT NULL COMMENT '组名',
    var $reserved_nr = 0; //TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是系统内置组',	
    var $note = ''; //VARCHAR(256) NULL COMMENT '备注说明',
    public function check_label($value, $data) {
        $where ['gname'] = $value;
        if (! empty ( $data ['gid'] )) {
            $where ['gid !='] = $data ['gid'];
        }
        return $this->exist ( $where ) ? "用户组标识已经存在" : true;
    }
}