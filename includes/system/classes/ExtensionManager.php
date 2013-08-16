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
    private $aliases = array ();
    private static $cacheKey = '_extensions_info';
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
    public function getAliasMap(){
        return $this->aliases ['m2u']?$this->aliases ['m2u']:array();
    }
    /**
     * 加载已经安装的插件
     */
    public function loadInstalledExtensions($load_init = true) {
        global $__kissgo_exports;
        // 加载用户安装插件        
        $cachedExtensions = InnerCacher::get ( self::$cacheKey );
        if ($cachedExtensions) {
            $this->extensions = $cachedExtensions;
        } else if (file_exists ( APPDATA_PATH . 'extensions.ini' )) {
            $this->extensions = parse_ini_file ( APPDATA_PATH . 'extensions.ini', true );
            if ($this->extensions !== false) {
                InnerCacher::add ( self::$cacheKey, $this->extensions );
            }
            if ($this->extensions === false && $load_init) {
                log_debug ( "extensions.ini的文件格式不正确，无法加载！" );
                return;
            }
        }
        if (empty ( $this->extensions )) {
            return;
        }
        if ($load_init) {
            $ps = array ('models', 'forms', 'libs', 'classes', 'common', 'includes' );
            foreach ( $this->extensions as $name => $plugin ) {
                if (isset ( $plugin ['disabled'] ) && $plugin ['disabled']) {
                    continue;
                }
                if (! isset ( $plugin ['Module'] )) {
                    continue;
                }
                $this->modules [] = $plugin ['Module'];
                if (isset ( $plugin ['alias'] ) && ! empty ( $plugin ['alias'] )) {
                    $this->aliases ['u2m'] [$plugin ['alias']] = $plugin ['Module'];
                    $this->aliases ['m2u'] [$plugin ['Module']] = $plugin ['alias'];
                }
                foreach ( $ps as $p ) {
                    $path = MODULES_PATH . $plugin ['Module'] . DS . $p;
                    if (is_dir ( $path )) {
                        $__kissgo_exports [] = $path;
                    }
                }
            }
            $this->load ( $this->modules );
        }
    }
    /**
     * 保存已经安装插件信息
     * @param array
     * @return boolean
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
                $pluginsStr [] = "{$key} = {$val}";
            }
        }
        $pluginsStr = implode ( "\n", $pluginsStr );
        if (@file_put_contents ( APPDATA_PATH . 'extensions.ini', $pluginsStr ) !== false) {
            InnerCacher::remove ( self::$cacheKey );
            return true;
        } else {
            log_debug ( '保存插件配置文件时出错.' );
        }
        return false;
    }
    public function getExension($mid) {
        if (isset ( $this->installed [$mid] )) {
            return $this->installed [$mid];
        } else if (isset ( $this->uninstalled [$mid] )) {
            return $this->uninstalled [$mid];
        } else {
            return false;
        }
    }
    /**
     * 
     * 安装模块
     * @param string $pid 模块ID
     * @return boolean|string
     */
    public function installExtension($pid, $unremovable = 0) {
        if (isset ( $this->uninstalled [$pid] )) {
            $extension = $this->uninstalled [$pid];
            $extension ['disabled'] = 0;
            $extension ['Installed'] = time ();
            $extension ['unremovable'] = $unremovable;
            include_once APP_PATH . $extension ['pkg_file'];
            $rst = apply_filter ( 'on_install_module_' . $pid, true );            
            if ($rst === true) {
                $this->extensions [] = $extension;
                $rst = $this->saveExtensionsData ();
                if ($rst) {
                    return true;
                } else {
                    return "无法保存扩展配置文件.";
                }
            } else {
                return $rst;
            }
        } else {
            return "插件不存在或已经安装.";
        }
    }
    /**
     * 
     * 升级模块
     * @param string $pid 模块ID
     * @return boolean
     */
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
    /**
     * 
     * 卸载模块
     * @param string $pid 模块ID
     * @return boolean
     */
    public function uninstallExtension($pid) {
        if (isset ( $this->extensions [$pid] )) {
            $rst = apply_filter ( 'on_uninstall_module_', true );
            if ($rst === true) {
                unset ( $this->extensions [$pid] );
                return $this->saveExtensionsData ();
            }
        }
        return false;
    }
    /**
     * 
     * 启用或禁用模块
     * @param string $pid 模块ID
     * @param boolean $enabled
     * @return boolean
     */
    public function enableExtension($pid, $enabled = 1) {
        if (isset ( $this->extensions [$pid] )) {
            $this->extensions [$pid] ['disabled'] = $enabled ? 0 : 1;
            return $this->saveExtensionsData ();
        } else {
            return false;
        }
    }
    /**
     * 
     * 根据别名查找模块
     * @param string $alias
     * @return string 模块
     */
    public function getModuleByAlias($alias) {
        if (isset ( $this->aliases ['u2m'] [$alias] )) {
            return $this->aliases ['u2m'] [$alias];
        } else if (in_array ( $alias, $this->modules )) {
            return $alias;
        }
        return null;
    }
    /**
     * 
     * 取模块的别名
     * @param string $module
     * @return string 别名
     */
    public function getAlias($module) {
        if (isset ( $this->aliases ['m2u'] [$module] )) {
            return $this->aliases ['m2u'] [$module];
        } else {
            return $module;
        }
    }
    public function setAlias($pid, $alias) {
        if (! isset ( $this->extensions [$pid] )) {
            return false;
        }
        if (empty ( $alias ) || ! preg_match ( '#^[0-9_a-z]+$#', $alias )) {
            unset ( $this->extensions [$pid] ['alias'] );
        } else if ($this->validateAlias ( $pid, $alias )) {
            $this->extensions [$pid] ['alias'] = $alias;
            $plugin = $this->extensions [$pid];
            $this->aliases ['u2m'] [$plugin ['alias']] = $plugin ['Module'];
            $this->aliases ['m2u'] [$plugin ['Module']] = $plugin ['alias'];
            return $this->saveExtensionsData ();
        }
        return false;
    }
    /**
     * 验证模块名或别名是否可用
     */
    public function validateAlias($pid, $alias) {
        foreach ( $this->extensions as $id => $ext ) {
            if (isset ( $ext ['alias'] ) && $ext ['alias'] == $alias && $pid != $id) {
                return false;
            }
            if ($ext ['Module'] == $alias) {
                return false;
            }
        }
        $hd = opendir ( WEB_ROOT );
        if ($hd) {
            while ( ($f = readdir ( $hd )) != false ) {
                if ($f == $alias) {
                    closedir ( $hd );
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 
     * 模块$module是否有别名
     * @param string $module
     * @return boolean
     */
    public function hasAlias($module) {
        return isset ( $this->aliases ['m2u'] [$module] );
    }
    /**
     * 取插件列表
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
    /**
     * 
     * 查找模块升级信息
     * @param boolean $upgrade
     */
    public function enableUpgradeInfo($upgrade = true) {
        $this->getUpgradeInfo = $upgrade;
    }
    /**
     * 
     * 加载插件信息
     * @param string $plugin_file
     * @return array represents module information
     */
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
            if (isset ( $extensions [$plugin ['Module_ID']] ['alias'] )) {
                $plugin ['alias'] = $extensions [$plugin ['Module_ID']] ['alias'];
            }
            $plugin ['upgradable'] = $this->isUpgradable ( $plugin, $extensions [$plugin ['Module_ID']] ['Version'] );
            if ($this->getUpgradeInfo) {
                $plugin ['curVersion'] = $extensions [$plugin ['Module_ID']] ['Version'];
            }
            $this->installed [$plugin ['Module_ID']] = $plugin;
        } else {
            $plugin ['Installed'] = false;
            $plugin ['disabled'] = 0;
            $plugin ['curVersion'] = $plugin ['Version'];
            $this->uninstalled [$plugin ['Module_ID']] = $plugin;
        }
        return $plugin;
    }
    /**
     * 
     * 加载模块
     * @param array $extensions
     * @param boolean $pkg 加载模块的包描述文件，还是加载模块的初始化文件
     */
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