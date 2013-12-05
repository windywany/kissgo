<?php
bind ( 'init_smarty_engine', 'on_init_smarty_engine' );
function on_init_smarty_engine($smarty) {
    $smarty->assign ( 'assetsurl', ASSETS_URL );
    $smarty->assign ( 'moduleurl', MODULE_URL );
    $smarty->assign ( 'moduledir', MODULE_DIR );
    $smarty->assign ( 'siteurl', BASE_URL );
    $smarty->assign ( 'admincp', ADMINCP_URL );
    return $smarty;
}
bind ( 'init_view_smarty_engine', 'on_init_smarty_view_engine' );
function on_init_smarty_view_engine($smarty) {
    $smarty->assign ( 'layout', 'admin/layout/layout.tpl' );
    $smarty->assign ( 'passport', whoami () );
    return $smarty;
}