<?php
/**
 * 插件管理器
 * 
 * @author LeoNing
 *
 */
class ExtensionManager {
    private static $INSTANCE = false;
    private $extensions = array ();
    private $plugins = array (); //enabled plugins
    private $modules = array (); //enabled modules    
    private $installed = array ();
    private $uninstalled = array ();
    private $getUpgradeInfo = false;
    /**
	 * 获取系统唯一插件管理器实例
	 *
	 * @return ExtensionManager
	 */
    public static function getInstance() {
        if (! self::$INSTANCE) {
            self::$INSTANCE = new ExtensionManager ();
        }
        return self::$INSTANCE;
    }
    /**
     * 加载已经安装的插件
     */
    public function loadInstalledExtensions() {
        // 加载用户安装插件
        if (file_exists ( APPDATA_PATH . 'extensions.ini' )) {
            $this->extensions = parse_ini_file ( APPDATA_PATH . 'extensions.ini', true );
            if ($this->extensions === false) {
                log_debug ( "extensions.ini的文件格式不正确，无法加载！" );
                return;
            }
            if (empty ( $this->extensions )) {
                return;
            }
            foreach ( $this->extensions as $name => $plugin ) {
                if (isset ( $plugin ['disabled'] ) && $plugin ['disabled']) {
                    continue;
                }
                if (! isset ( $plugin ['Plugin'] )) {
                    continue;
                }
                if ($plugin ['type'] == 'plugin') {
                    $this->plugins [] = $plugin ['Plugin'];
                } else {
                    $this->modules [] = $plugin ['Plugin'];
                }
            }
            $this->load ( $this->modules );
            $this->load ( $this->plugins, 'plugin' );
        }
    }
    /**
	 * 保存已经安装插件信息
	 */
    public function saveExtensionsData($extensions = array()) {
        if (empty ( $extensions )) {
            $extensions = $this->extensions;
        }
        $pluginsStr = array ();
        foreach ( $extensions as $o ) {
            $pluginsStr [] = "[{$o['Plugin_ID']}]";
            foreach ( $o as $key => $val ) {
                $val = str_replace ( '!', '！', $val );
                $pluginsStr [] = "\t{$key} = {$val}";
            }
        }
        $pluginsStr = implode ( "\n", $pluginsStr );
        if (@file_put_contents ( APPDATA_PATH . 'extensions.ini', $pluginsStr ) !== false) {
            return true;
        } else {
            log_debug ( '保存插件配置文件时出错.' );
        }
        return false;
    }
    public function installExtension($pid, $type = "module") {
        if (isset ( $this->uninstalled [$type] [$pid] )) {
            $extension = $this->uninstalled [$type] [$pid];            
            $extension ['disabled'] = 0;
            $extension ['Installed_Time'] = time ();
            $plugin ['unremovable'] = 0;
            $this->extensions [] = $extension;
            $rst = $this->saveExtensionsData ();
            if ($rst) {
                return true;
            } else {
                return "无法保存扩展配置文件.";
            }
        } else {
            return "插件不存在或已经安装.";
        }
    }
    public function upgradeExtension($pid) {
        if (isset ( $this->extensions [$pid] )) {
            $file = APP_PATH . $this->extensions [$pid] ['pkg_file'];
            $plugin = $this->getExensionInfo ( $file, $this->extensions [$pid] ['core'], $this->extensions [$pid] ['type'] );
            if ($plugin) {
                unset ( $plugin ['upgradable'] );
                $this->extensions [$pid] = $plugin;
                return $this->saveExtensionsData ();
            }
        } else {
            return false;
        }
    }
    public function uninstallExtension($pid) {
        if (isset ( $this->extensions [$pid] )) {
            unset ( $this->extensions [$pid] );
            return $this->saveExtensionsData ();
        } else {
            return false;
        }
    }
    public function enableExtension($pid, $enabled = 1) {
        if (isset ( $this->extensions [$pid] )) {
            $this->extensions [$pid] ['disabled'] = $enabled ? 0 : 1;
            return $this->saveExtensionsData ();
        } else {
            return false;
        }
    }
    /**
	 * 取插件系统
	 *
	 * @param boolean $installed        	
	 * @return ArrayObject
	 */
    public function getExtensions($installed = true, $type = 'module') {
        static $scaned = false;
        if (! $scaned) {
            $scaned = true;
            $this->scanExentions ();
        }
        $extensions = $installed ? $this->installed [$type] : $this->uninstalled [$type];
        if (is_array ( $extensions )) {
            return $extensions;
        }
        return array ();
    }
    public function enableUpgradeInfo($upgrade = true) {
        $this->getUpgradeInfo = $upgrade;
    }
    // 加载插件信息
    public function getExensionInfo($plugin_file, $core = 0, $type = 'module') {
        $content = file_get_contents ( $plugin_file );
        if (empty ( $content )) {
            return false;
        }
        $plugin = array ();
        if (preg_match ( '/Plugin\s+ID\s*:\s+(.*)/', $content, $name )) {
            $plugin ['Plugin_ID'] = trim ( $name [1] );
        } else {
            $plugin ['Plugin_ID'] = basename ( $plugin_file, '.php' );
        }
        if (preg_match ( '/Plugin\s+Name\s*:\s+(.*)/', $content, $name )) {
            $plugin ['Plugin_Name'] = trim ( $name [1] );
        } else {
            $plugin ['Plugin_Name'] = basename ( $plugin_file, '.php' );
        }
        if (preg_match ( '/Plugin\s+URI\s*:\s+(.*)/', $content, $URI )) {
            $plugin ['Plugin_URI'] = trim ( $URI [1] );
        } else {
            $plugin ['Plugin_URI'] = '#';
        }
        if (preg_match ( '/Author\s*:\s+(.*)/', $content, $Author )) {
            $plugin ['Author'] = trim ( $Author [1] );
        } else {
            $plugin ['Author'] = '未知';
        }
        if (preg_match ( '/Version\s*:\s+(.*)/', $content, $Version )) {
            $plugin ['Version'] = trim ( $Version [1] );
        } else {
            $plugin ['Version'] = '0';
        }
        if (preg_match ( '/Author\s+URI\s*:\s+(.*)/', $content, $URI )) {
            $plugin ['Author_URI'] = trim ( $URI [1] );
        } else {
            $plugin ['Author_URI'] = '#';
        }
        if (preg_match ( '/Description\s*:\s+(.*)/', $content, $desc )) {
            $plugin ['Description'] = trim ( $desc [1] );
        } else {
            $plugin ['Description'] = '';
        }
        $module_apath = $core ? KISSGO . 'modules' . DS : MODULES_PATH;
        $module_rpath = $core ? '::' : '';
        $plugin ['Plugin'] = str_replace ( array (
                                                    $module_apath, 
                                                    DS . '__pkg__.php', 
                                                    DS 
        ), array (
                $module_rpath, 
                '', 
                '/' 
        ), str_replace ( '/', DS, $plugin_file ) );
        $plugin ['core'] = $core;
        $plugin ['type'] = $type;
        $plugin ['pkg_file'] = str_replace ( APP_PATH, '', $plugin_file );
        $extensions = $this->extensions;
        if (isset ( $extensions [$plugin ['Plugin_ID']] )) {
            $plugin ['Installed'] = true;
            $plugin ['disabled'] = $extensions [$plugin ['Plugin_ID']] ['disabled'];
            $plugin ['Installed_Time'] = $extensions [$plugin ['Plugin_ID']] ['Installed_Time'];
            $plugin ['unremovable'] = $extensions [$plugin ['Plugin_ID']] ['unremovable'];
            $plugin ['upgradable'] = $this->isUpgradable ( $plugin, $extensions [$plugin ['Plugin_ID']] ['Version'] );
            if ($this->getUpgradeInfo) {
                $plugin ['curVersion'] = $extensions [$plugin ['Plugin_ID']] ['Version'];
            }
            $this->installed [$type] [$plugin ['Plugin_ID']] = $plugin;
        } else {
            $plugin ['Installed'] = false;
            $plugin ['disabled'] = 0;
            $plugin ['Installed_Time'] = 0;
            $this->uninstalled [$type] [$plugin ['Plugin_ID']] = $plugin;
        }
        return $plugin;
    }
    public function load($extensions, $type = 'module', $pkg = false) {
        global $_ksg_installed_modules, $_ksg_installed_plugins;
        if (is_array ( $extensions )) {
            $app_init_files = array ();
            foreach ( $extensions as $app ) {
                $app_path = $app;
                if (preg_match ( '/^::/', $app )) {
                    $app_path = ltrim ( $app, ':' );
                    
                    $app_init_files [] = str_replace ( '::', "::{$type}s/", $app ) . ($pkg ? '/__pkg__.php' : '/__init__.php');
                } else {
                    $app_init_files [] = ($type == 'module' ? MODULE_DIR : PLUGIN_PATH) . '/' . $app . ($pkg ? '/__pkg__.php' : '/__init__.php');
                }
                if (! $pkg) {
                    if ($type == 'module') {
                        $_ksg_installed_modules [] = $app_path;
                    } else {
                        $_ksg_installed_plugins [] = $app_path;
                    }
                }
            }
            if (! empty ( $app_init_files )) {
                includes ( $app_init_files );
            }
        }
    }
    // 扫描插件信息
    private function scanExentions() {
        $coreModules = find_files ( KISSGO . 'modules', '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $coreModules )) {
            foreach ( $coreModules as $plugin_file ) {
                $this->getExensionInfo ( $plugin_file, 1 );
            }
        }
        $coreModules = find_files ( KISSGO . 'plugins', '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $coreModules )) {
            foreach ( $coreModules as $plugin_file ) {
                $this->getExensionInfo ( $plugin_file, 1, 'plugin' );
            }
        }
        $allPlugins = find_files ( PLUGIN_PATH, '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $allPlugins )) {
            foreach ( $allPlugins as $plugin_file ) {
                $this->getExensionInfo ( $plugin_file, 0, 'plugin' );
            }
        }
        $allPlugins = find_files ( MODULES_PATH, '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $allPlugins )) {
            foreach ( $allPlugins as $plugin_file ) {
                $this->getExensionInfo ( $plugin_file );
            }
        }
    }
    private function isUpgradable($plugin, $curVersion) {
        //if($this->getUpgradeInfo){
        //}
        return version_compare ( $plugin ['Version'], $curVersion, '>' );
    }
}