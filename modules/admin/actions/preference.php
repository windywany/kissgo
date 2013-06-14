<?php
/*
 * 选项
 */
assert_login ();
function do_admin_preference_get($req, $res) {
    $group = rqst ( '_g', 'base' );
    $data = array ();
    $optM = new KsgPreferenceTable ();
    $data ['__options'] = apply_filter ( 'get_option_' . $group, array () );
    
    if (empty ( $data ['__options'] )) {
        $opts = $optM->query ()->where ( array ('group' => $group ) );
        $data ['__options'] = count ( $opts ) ? $opts->toArray ( 'name', 'value' ) : array ();
    }
    $data ['_g'] = $group;
    bind ( 'get_core_option_group', '_hook_get_core_option_group', 0 );
    
    $data ['__opt_groups'] = apply_filter ( 'get_core_option_group', array () );
    $data ['__option_tpl'] = apply_filter ( "get_{$group}_option_tpl", false, $data ['__options'] );
    if ($data ['__option_tpl'] instanceof BaseForm) {
        $data ['__option_form'] = $data ['__option_tpl'];
        unset ( $data ['__option_tpl'] );
    }
    $data ['_CUR_URL'] = murl ( 'admin', 'preference' );
    return view ( 'admin/views/preference.tpl', $data );
}


function _hook_get_core_option_group($groups) {
    $groups ['base'] = '<i class="icon-cog"></i> 基本设置';
    $groups ['safe'] = '<i class="icon-fire"></i> 安全设置';
    $groups ['thumb'] = '<i class="icon-picture"></i> 图片与水印';
    $groups ['smtp'] = '<i class="icon-envelope"></i> 邮件设置';
    return $groups;
}