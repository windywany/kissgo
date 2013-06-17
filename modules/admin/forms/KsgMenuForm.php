<?php
/**
 * 导航菜单表单
 * @author Leo
 *
 */
class KsgMenuForm extends DataForm {
    var $menu_id = array (FWT_VALIDATOR => array ('digits' => '导航菜单编号只能是数字.' ) );
    var $menu_name = array (FWT_VALIDATOR => array ('callback(@check_menu_name)' => '菜单不能重名.' ) );
    var $menu_title = array ();    
}