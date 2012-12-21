<?php
/**
 * 模块管理
 */
assert_login ();

$plgmgr = PluginManager::getInstance ();

$plugins = $plgmgr->getExtensions ( false );

return admin_view ( 'kissgo/index.tpl' );