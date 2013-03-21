<?php
assert_login ();
function do_admin_cmb_get($res, $req) {
    $data = array ();
    $data ['model_form'] = new CmbModelForm();
    return view ( 'admin/views/cmb.tpl',$data );
}

class CmbModelForm extends BootstrapForm {
    var $model_name = array(FWT_LABEL=>'Model Name',FWT_VALIDATOR=>array('regex(/^[a-z][a-z0-9_]*$/i)'=>'Model Name must start with.'));
}