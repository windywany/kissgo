<?php
/**
 * 
 * @author Leo
 *
 */
class KsgMenuItemForm extends DataForm {
    var $item_name = array (FWT_VALIDATOR => array ('required' => '请输入菜单项的名称.' ) );
    var $title = array ();
    var $url = array (FWT_VALIDATOR => array ('required' => '请输入菜单项的URL.', 'url' => '请输入正确的URL.' ) );
    var $target = array (FWT_VALIDATOR => array ('regexp(/^_(blank|self)$/)' => '目标只能是_blank或_self' ) );
    var $menu_name = array ();
}