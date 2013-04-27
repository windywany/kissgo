<?php
assert_login ();
function do_admin_cmb_get($res, $req) {
    $data = array ();
    $data ['model_form'] = new CmbModelForm ();
    $data ['types'] = array ('varchar:normal' => 'VARCHAR', 'char:normal' => 'CHAR', 'text:tiny' => 'TINYTEXT', 'text:small' => 'TINYTEXT', 'text:medium' => 'MEDIUMTEXT', 'text:big' => 'LONGTEXT', 'text:normal' => 'TEXT', 'serial:tiny' => 'TINYINT', 'serial:small' => 'SMALLINT', 'serial:medium' => 'MEDIUMINT', 'serial:big' => 'BIGINT', 'serial:normal' => 'INT', 'int:tiny' => 'TINYINT', 'int:small' => 'SMALLINT', 'int:medium' => 'MEDIUMINT', 'int:big' => 'BIGINT', 'int:normal' => 'INT', 
            'bool:normal' => 'TINYINT', 'float:tiny' => 'FLOAT', 'float:small' => 'FLOAT', 'float:medium' => 'FLOAT', 'float:big' => 'DOUBLE', 'float:normal' => 'FLOAT', 'numeric:normal' => 'DECIMAL', 'blob:big' => 'LONGBLOB', 'blob:normal' => 'BLOB', 'timestamp:normal' => 'INT', 'date:normal' => 'DATE', 'datetime:normal' => 'DATETIME','enum:normal'=>'ENUM' );
    return view ( 'admin/views/cmb.tpl', $data );
}

class CmbModelForm extends BootstrapForm {
    var $model_name = array (FWT_LABEL => 'Model Name' );
    var $model_table = array (FWT_LABEL => 'Table Name' );
    var $model_desc = array (FWT_LABEL => 'Description', FWT_OPTIONS => array ('class' => 'input-xxlarge' ) );
}