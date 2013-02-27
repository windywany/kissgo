<?php
/*
 * 选项
 */
assert_login ();
imports ( 'admin/models/*' );
function do_admin_preference_get($req, $res) {
    $group = rqst ( 'group', 'base' );
    $data = array ();
    $optM = new CorePreferenceTable ();
    $opts = $optM->query ()->where ( array ('group' => $group ) );
    $data ['options'] = apply_filter ( 'get_option_' . $group, array () );
    if (empty ( $data ['options'] )) {
        $data ['options'] = count ( $opts ) ? $opts->toArray ( 'name', 'value' ) : array ();
    }
    $data ['group'] = $group;
    bind ( 'get_core_option_group', '_hook_get_core_option_group', 0 );
    $data ['opt_groups'] = apply_filter ( 'get_core_option_group', array () );
    $data ['option_tpl'] = apply_filter ( "get_{$group}_option_tpl", false, $data ['options'] );
    if ($data ['option_tpl'] instanceof BaseForm) {
        $data ['option_form'] = $data ['option_tpl'];
        unset ( $data ['option_tpl'] );
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