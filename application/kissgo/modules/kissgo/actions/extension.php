<?php
/**
 * 模块管理
 */
assert_login ();

$req = Request::getInstance ();

$plgmgr = PluginManager::getInstance ();

$plugins = $plgmgr->getExtensions ( false );

return admin_view ( 'kissgo/extension.tpl', array (
                                                    'extensions' => $plugins, 
                                                    'type' => 'module' 
) );