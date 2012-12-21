<?php
/**
 * 插件管理器
 * 
 * @author LeoNing
 *
 */
class PluginManager {
    private static $INSTANCE = false;
    private $extensions = array ();
    private $plugins = array (); //enabled plugins
    private $modules = array (); //enabled modules
    private $allPlugins = array ();
    private $allModules = array ();
    private $installed = array ();
    private $uninstalled = array ();
    /**
	 * 获取系统唯一插件管理器实例
	 *
	 * @return PluginManager
	 */
    public static function getInstance() {
        if (! self::$INSTANCE) {
            self::$INSTANCE = new PluginManager ();
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
            $this->load_modules ( $this->modules );
            $this->load_plugins ( $this->plugins );
        }
    }
    /**
	 * 插件$pluginID是否启用
	 *
	 * @param string $pluginID        	
	 * @return boolean
	 */
    public static function enabled($pluginID, $forbidden = true) {
        static $pm = false;
        if (! $pm) {
            $pm = self::getInstance ();
        }
        $enabled = $pm->isEnabled ( $pluginID );
        if ($enabled) {
            return true;
        } else if (! $forbidden) {
            return false;
        } else {
            Response::error ( '', 403, true );
        }
    }
    public function isEnabled($pluginID) {
        return isset ( $this->plugins [$pluginID] ) && empty ( $this->plugins [$pluginID] ['disabled'] );
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
    // 扫描插件信息
    private function scanPlugins() {
        $coreModules = find_files ( KISSGO . 'modules', '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $coreModules )) {
            foreach ( $coreModules as $plugin_file ) {
                $this->loadExtension ( $plugin_file, 1 );
            }
        }
        $coreModules = find_files ( KISSGO . 'plugins', '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $coreModules )) {
            foreach ( $coreModules as $plugin_file ) {
                $this->loadExtension ( $plugin_file, 1, 'plugin' );
            }
        }
        $allPlugins = find_files ( PLUGIN_PATH, '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $allPlugins )) {
            foreach ( $allPlugins as $plugin_file ) {
                $this->loadExtension ( $plugin_file, 0, 'plugin' );
            }
        }
        $allPlugins = find_files ( MODULES_PATH, '/^__pkg__\.php$/', array (), 1, 1 );
        if (! empty ( $allPlugins )) {
            foreach ( $allPlugins as $plugin_file ) {
                $this->loadExtension ( $plugin_file );
            }
        }
    }
    private function loadExtension($file, $core = 0, $type = 'module') {
        $plg = $this->getExensionInfo ( $file, $core, $type );
        if ($plg) {
            if ($type == 'module') {
                $this->allModules [$plg ['Plugin_ID']] = $plg;
            } else {
                $this->allPlugins [$plg ['Plugin_ID']] = $plg;
            }
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
            $this->scanPlugins ();
        }
        return $installed ? $this->installed [$type] : $this->uninstalled [$type];
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
            $plugin ['Version'] = '';
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
        $extensions = $this->extensions;
        if (isset ( $extensions [$plugin ['Plugin_ID']] )) {
            $plugin ['Installed'] = true;
            $plugin ['disabled'] = $extensions [$plugin ['Plugin_ID']] ['disabled'];
            $plugin ['Installed_Time'] = $extensions [$plugin ['Plugin_ID']] ['installed'];
            $this->installed [$type] [$plugin ['Plugin_ID']] = $plugin;
        } else {
            $plugin ['Installed'] = false;
            $plugin ['disabled'] = 0;
            $plugin ['Installed_Time'] = 0;
            $this->uninstalled [$type] [$plugin ['Plugin_ID']] = $plugin;
        }
        return $plugin;
    }
    public function load_modules($modules) {
        global $_ksg_installed_modules;
        if (is_array ( $modules )) {
            $app_init_files = array ();
            foreach ( $modules as $app ) {
                $app_path = $app;
                if (preg_match ( '/^::/', $app )) {
                    $app_path = ltrim ( $app, ':' );
                    $app_init_files [] = str_replace ( '::', '::modules/', $app ) . '/__init__.php';
                } else {
                    $app_init_files [] = MODULE_DIR . '/' . $app . '/__init__.php';
                }
                $_ksg_installed_modules [] = $app_path;
            }
            if (! empty ( $app_init_files )) {
                includes ( $app_init_files );
            }
        }
    }
    public function load_plugins($plugins) {
        if (is_array ( $plugins ) && ! empty ( $plugins )) {
            $plg_init_files = array ();
            foreach ( $plugins as $plg ) {
                $app_path = $plg;
                if (preg_match ( '/^::/', $plg )) {
                    $app_path = ltrim ( $plg, ':' );
                    $plg_init_files [] = str_replace ( '::', '::plugins/', $plg );
                } else {
                    $plg_init_files [] = 'plugins/' . $plg;
                }
                $_ksg_installed_plugins [] = $app_path;
            }
            if (! empty ( $plg_init_files )) {
                includes ( $plg_init_files );
            }
        }
    }
}