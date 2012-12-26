<?php
/**
 * 模块管理
 */
assert_login ();
$req = Request::getInstance ();
if (isset ( $req ['setup'] )) {
    $view = setup_extension ( $req ['setup'], $req ['type'] );
} else if (isset ( $req ['enable'] )) {
    $req = Request::getInstance ();
    $plgmgr = ExtensionManager::getInstance ();
    $rst = $plgmgr->enableExtension ( $req ['enable'], $req ['enabled'] );
    if ($rst) {
        Response::redirect ( murl ( 'kissgo', 'extension' ) );
    } else {
        show_error_message ( $req ['enabled'] ? '启用扩展失败.' : '停用扩展失败.' );
    }
} else {
    $view = list_extension ();
}
return $view;
/**
 * 安装扩展
 */
function setup_extension($pid, $type) {
    $opTexts = array (
                    'install' => '安装', 
                    'upgrade' => '升级', 
                    'uninstall' => '卸载' 
    );
    $req = Request::getInstance ();
    $operation = $req->get ( 'op', 'install' );
    if (! isset ( $opTexts [$operation] )) {
        show_error_message ('未实现的操作.' );
    } else {
        $data ['op_title'] = $opTexts [$operation];
    }
    $data ['_page_url'] = murl ( 'kissgo', 'extension' );
    $plgmgr = ExtensionManager::getInstance ();
    $extensions = $plgmgr->getExtensions ( $operation == 'install' ? false : true, $type );
    if (isset ( $extensions [$pid] )) {
        $plgmgr->load ( array (
                                $extensions [$pid] ['Plugin'] 
        ), $type, ture );
        $tpl = 'kissgo/extension/done.tpl';
        $data ['extName'] = $extensions [$pid] ['Plugin_Name'];
        $data ['form'] = '';
        $data ['pid'] = $pid;
        $data ['type'] = $type;
        $data ['operation'] = $operation;
        if (isset ( $req ['step'] )) {
            $data ['success'] = true;
            switch ($req ['step']) {
                case 'tasks' :                    
                    $tasks = apply_filter ( "the_{$operation}_tasks_" . ltrim ( $extensions [$pid] ['Plugin'] ), array () );
                    $tasks [] = array (
                                    'text' => '保存扩展信息', 
                                    'step' => 'saveExtInfo', 
                                    'weight' => 10 
                    );
                    
                    $data ['tasks'] = $tasks;
                    break;
                case 'saveExtInfo' :
                    if ($operation == 'install') {
                        $rst = $plgmgr->installExtension ( $pid, $type );
                    } else if ($operation == 'upgrade') {
                        $rst = $plgmgr->upgradeExtension ( $pid );
                    } else if ($operation == 'uninstall') {
                        $rst = $plgmgr->uninstallExtension ( $pid );
                    } else {
                        $rst = '未知操作,系统无法完成.';
                    }
                    if ($rst !== true) {
                        $data ['success'] = false;
                        $data ['msg'] = $rst;
                    }
                    break;
                default :
                    $rst = apply_filter ( "the_{$operation}_do_task_" . ltrim ( $extensions [$pid] ['Plugin'] ), true, $req ['step'] );
                    if ($rst !== true) {
                        $data ['success'] = false;
                        $data ['msg'] = $rst;
                    }
                    break;
            }
            return new JsonView ( $data );
        } else if (! isset ( $req ['done'] )) {
            $form = apply_filter ( "the_{$operation}_form_" . ltrim ( $extensions [$pid] ['Plugin'] ), false );
            if ($form) {
                $tpl = 'kissgo/extension/setup.tpl';
                $data ['form'] = $form;
            }
        }
        return admin_view ( $tpl, $data );
    } else { //插件不存在
        show_error_message ("Extension $pid does not exist!" );
    }
}
/**
 * 列出扩展：(已安装，未安装) * (已启用,可升级,未启用)
 * @return SmartyView
 */
function list_extension() {     
    $req = Request::getInstance ();
    $plgmgr = ExtensionManager::getInstance ();
    $plgmgr->enableUpgradeInfo ();
    $data ['group'] = $req->get ( 'group', 'installed' );
    $plugins = $plgmgr->getExtensions ( true );
    $plugins = $plugins + $plgmgr->getExtensions ( true, 'plugin' );
    $installedTotal = count ( $plugins );
    $uplugins = $plgmgr->getExtensions ( false );
    $uplugins = $uplugins + $plgmgr->getExtensions ( false, 'plugin' );
    $uninstalledTotal = count ( $uplugins );
    $etotal = $dtotal = $upgradable = 0;
    $data ['items'] = $uplugins;
    foreach ( $plugins as $p ) {
        if ($p ['disabled']) {
            $dtotal ++;
        } else {
            $etotal ++;
        }
        if ($p ['upgradable']) {
            $upgradable ++;
        }
    }
    $data ['dtotal'] = $dtotal;
    $data ['etotal'] = $etotal;
    $data ['upgradable'] = $upgradable;
    if ($data ['group'] == 'installed') {
        $data ['items'] = $plugins;
    }
    $data ['uninstalledTotal'] = $uninstalledTotal;
    $data ['installedTotal'] = $installedTotal;
    $data ['_page_url'] = murl ( 'kissgo', 'extension' );
    bind ( 'get_plugin_operation', '_hook_plugin_operation', 10, 2 );
    $view = admin_view ( 'kissgo/extension/extension.tpl', $data );
    return $view;
}
// hook for get_plugin_operation
function _hook_plugin_operation($ops, $item) {
    static $url = false;
    if (! $url) {
        $url = murl ( 'kissgo', 'extension' );
    }
    if ($item ['Installed']) { // 已经安装
        if ($item ['disabled']) {
            $ops .= '<a href="' . $url . '?enable=' . $item ['Plugin_ID'] . '&enabled=1" class="btn-enable" onclick="return confirm(\'你确定要启用用这个扩展吗?\');">启用</a>';
        } else if ($item ['unremovable'] != '1') {
            $ops .= '<a href="' . $url . '?enable=' . $item ['Plugin_ID'] . '&enabled=0" class="btn-disabled" onclick="return confirm(\'你确定要停用这个扩展吗?\');">停用</a>';
            if ($item ['upgradable']) {
                $ops .= '<a href="' . $url . '?setup=' . $item ['Plugin_ID'] . '&type=' . $item ['type'] . '&op=upgrade" class="upgrade" onclick="return confirm(\'你确定要升级这个扩展吗?\');">升级</a>';
            }
        }
        if ($item ['unremovable'] != '1') {
            $ops .= '<a href="' . $url . '?setup=' . $item ['Plugin_ID'] . '&type=' . $item ['type'] . '&op=uninstall" class="uninstall" onclick="return confirm(\'你确定要卸载这个扩展吗?\');">卸载</a>';
        }
    } else {
        $ops .= '<a href="' . $url . '?setup=' . $item ['Plugin_ID'] . '&type=' . $item ['type'] . '" class="install" onclick="return confirm(\'你确定要安装这个扩展吗?\');">安装</a>';
    }
    return $ops;
}