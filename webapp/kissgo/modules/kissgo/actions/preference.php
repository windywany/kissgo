<?php
/*
 * 选项
 */
assert_login ();
imports ( 'kissgo/models/*', 'kissgo/kissgo_hooks.php' );
if (Request::isPost ()) {
}
$group = rqst ( 'group', 'base' );
$data = array ();
$optM = new PreferenceEntity ();
$opts = $optM->where ( array ('option_group' => $group ) )->retrieve ();
$data ['options'] = apply_filter ( 'get_option_' . $group, array () );
if (empty ( $data ['options'] )) {
    $data ['options'] = $opts ? $opts->toArray ( 'option_name', 'option_value' ) : array ();
}
$data ['group'] = $group;
bind ( 'get_core_option_group', '_hook_get_core_option_group', 0 );
bind ( 'show_option_control', array ($this, 'show_option_control' ), 0, 3 );
bind ( 'show_option_control', array ($this, 'show_smtp_option_control' ), 1, 3 );
bind ( 'show_option_control', array ($this, 'show_sitemap_option_control' ), 1, 3 );
bind ( 'show_option_control', array ($this, 'show_rss_option_control' ), 1, 3 );

$data ['opt_groups'] = apply_filter ( 'get_core_option_group', array () );
// 加载模板，这时可以方便的写一些js脚本在里边
$data ['option_tpl'] = apply_filter ( "get_{$group}_option_tpl", false, $data ['options'] );
return admin_view ( 'kissgo/preference.tpl', $data );
