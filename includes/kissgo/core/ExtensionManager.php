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
                if (! isset ( $plugin ['Module'] )) {
                    continue;
                }
                $this->modules [] = $plugin ['Module'];
            }
            $this->load ( $this->modules );
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
            $pluginsStr [] = "[{$o['Module_ID']}]";
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
    public function installExtension($pid) {
        if (isset ( $this->uninstalled [$pid] )) {
            $extension = $this->uninstalled [$pid];
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
            $plugin = $this->getExensionInfo ( $file );
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
    public function getExtensions($installed = true) {
        static $scaned = false;
        if (! $scaned) {
            $scaned = true;
            $this->scanExentions ();
        }
        $extensions = $installed ? $this->installed : $this->uninstalled;
        if (is_array ( $extensions )) {
            return $extensions;
        }
        return array ();
    }
    public function enableUpgradeInfo($upgrade = true) {
        $this->getUpgradeInfo = $upgrade;
    }
    // 加载插件信息
    public function getExensionInfo($plugin_file) {
        $content = file_get_contents ( $plugin_file );
        if (empty ( $content )) {
            return false;
        }
        $plugin = array ();
        if (preg_match ( '/Module\s+ID\s*:\s+(.*)/', $content, $name )) {
            $plugin ['Module_ID'] = trim ( $name [1] );
        } else {
            $plugin ['Module_ID'] = basename ( $plugin_file, '.php' );
        }
        if (preg_match ( '/Module\s+Name\s*:\s+(.*)/', $content, $name )) {
            $plugin ['Module_Name'] = trim ( $name [1] );
        } else {
            $plugin ['Module_Name'] = basename ( $plugin_file, '.php' );
        }
        if (preg_match ( '/Module\s+URI\s*:\s+(.*)/', $content, $URI )) {
            $plugin ['Module_URI'] = trim ( $URI [1] );
        } else {
            $plugin ['Module_URI'] = '#';
        }
        if (preg_match ( '/Author\s*:\s+(.*)/', $content, $Author )) {
            $plugin ['Author'] = trim ( $Author [1] );
        } else {
            $plugin ['Author'] = '未知';
        }
        if (preg_match ( '/Version\s*:\s+(.*)/', $content, $Version )) {
            $plugin ['Version'] = trim ( $Version [1] );
        } else {
            $plugin ['Version'] = '0.1';
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
        $plugin ['Module'] = str_replace ( array (MODULES_PATH, DS . '__pkg__.php', DS ), array ('', '', '/' ), str_replace ( '/', DS, $plugin_file ) );
        $plugin ['pkg_file'] = str_replace ( APP_PATH, '', $plugin_file );
        $extensions = $this->extensions;
        if (isset ( $extensions [$plugin ['Module_ID']] )) {
            $plugin ['Installed'] = true;
            $plugin ['disabled'] = $extensions [$plugin ['Module_ID']] ['disabled'];
            $plugin ['unremovable'] = $extensions [$plugin ['Module_ID']] ['unremovable'];
            $plugin ['core'] = $extensions [$plugin ['Module_ID']] ['core'];
            $plugin ['upgradable'] = $this->isUpgradable ( $plugin, $extensions [$plugin ['Module_ID']] ['Version'] );
            if ($this->getUpgradeInfo) {
                $plugin ['curVersion'] = $extensions [$plugin ['Module_ID']] ['Version'];
            }
            $this->installed [$plugin ['Module_ID']] = $plugin;
        } else {
            $plugin ['Installed'] = false;
            $plugin ['disabled'] = 0;
            $this->uninstalled [$plugin ['Module_ID']] = $plugin;
        }
        return $plugin;
    }
    public function load($extensions, $pkg = false) {
        global $_ksg_installed_modules;
        if (is_array ( $extensions )) {
            $app_init_files = array ();
            foreach ( $extensions as $app ) {
                $app_init_files [] = MODULE_DIR . '/' . $app . ($pkg ? '/__pkg__.php' : '/__init__.php');
                if (! $pkg) {
                    $_ksg_installed_modules [] = $app;
                }
            }
            if (! empty ( $app_init_files )) {
                includes ( $app_init_files );
            }
        }
    }
    // 扫描插件信息
    private function scanExentions() {
        $allModules = find_files ( MODULES_PATH, '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $allModules )) {
            foreach ( $allModules as $plugin_file ) {
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