<?php
/**
 * 插件管理器
 * 
 * @author LeoNing
 *
 */
class PluginManager {
	private static $INSTANCE = false;
	private $plugins = array ();
	private $allPlugins = array ();
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
	public function loadPlugins() {
		// 加载用户安装插件
		if (file_exists ( APPDATA_PATH . 'plugins.ini' )) {
			$plugins = parse_ini_file ( APPDATA_PATH . 'plugins.ini', true );
			if ($plugins === false) {
				log_debug ( "plugins.ini的文件格式不正确，无法加载！" );
				return;
			}
			if (empty ( $plugins )) {
				return;
			}
			foreach ( $plugins as $name => $plugin ) {
				if (isset ( $plugin ['disabled'] ) && $plugin ['disabled']) {
					continue;
				}
				if (! isset ( $plugin ['Plugin'] ) || ! file_exists ( APP_PATH . $plugin ['Plugin'] )) {
					continue;
				}
				require_once APP_PATH . $plugin ['Plugin'];
			}
			$this->plugins = $plugins;
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
	 * 强制加载插件，用于系统设置时
	 *
	 * @param string $pid        	
	 */
	public function forceLoadPlugin($pid) {
		if (isset ( $this->plugins [$pid] )) {
			$plugin = $this->plugins [$pid];
			if (isset ( $plugin ['Plugin'] ) && file_exists ( APP_PATH . $plugin ['Plugin'] )) {
				require_once APP_PATH . $plugin ['Plugin'];
			}
		}
	}
	/**
	 * 保存已经安装插件信息
	 */
	public function savePluginData() {
		$plugins = $this->plugins;
		$pluginsStr = array ();
		foreach ( $plugins as $p => $o ) {
			$pluginsStr [] = "[{$p}]";
			foreach ( $o as $key => $val ) {
				$pluginsStr [] = "\t{$key} = {$val}";
			}
		}
		$pluginsStr = implode ( "\n", $pluginsStr );
		if (@file_put_contents ( APPDATA_PATH . 'plugins.ini', $pluginsStr ) !== false) {
			return true;
		} else {
			log_debug ( '保存插件配置文件时出错.' );
		}
		return false;
	}
	// 扫描插件信息
	private function scanPlugins() {
		$plugins = find_files ( PLUGIN_PATH, '/^__init__\.php$/', array (), 1, 1 );
		$mplugins = find_files ( MODULES_PATH, '/^__init__\.php$/', array (), 1, 1 );
		$allPlugins = array_merge ( $plugins, $mplugins );
		if (! empty ( $allPlugins )) {
			foreach ( $allPlugins as $plugin_file ) {
				$plg = $this->loadPlugin ( $plugin_file );
				if ($plg) {
					$this->allPlugins [$plg ['Plugin_ID']] = $plg;
				}
			}
		}
	}
	/**
	 * 安装插件
	 *
	 * @param string $pid        	
	 * @return boolean
	 */
	public function installPlugin($pid) {
		$this->scanPlugins ();
		if (isset ( $this->installed [$pid] )) {
			return $this->installed [$pid];
		}
		$plugin = isset ( $this->uninstalled [$pid] ) ? $this->uninstalled [$pid] : false;
		if (! $plugin) {
			log_debug ( "安装插件:$pid 时，在未安装插件列表中未找到该插件." );
			return false;
		}
		
		$pfile = APP_PATH . $plugin ['Plugin'];
		if (file_exists ( $pfile )) {
			include_once $pfile;
			$rst = apply_filter ( 'install_plugin_' . $pid, true );
			if ($rst) {
				$plg ['Plugin'] = $plugin ['Plugin'];
				$plg ['disabled'] = 0; // 安装后直接启用
				$plg ['installed'] = time ();
				$this->plugins [$pid] = $plg;
				$rst = $this->savePluginData ();
				if (! $rst) {
					log_debug ( "安装插件:$pid 时，安装plugin.ini文件失败." );
				} else {
					return $plugin;
				}
			}
		} else {
			log_debug ( "安装插件:$pid 时，未找到该插件文件." );
		}
		return false;
	}
	/**
	 * 卸载插件
	 *
	 * @param string $pid        	
	 */
	public function uninstallPlugin($pid) {
		$this->scanPlugins ();
		if (! isset ( $this->plugins [$pid] )) {
			return true;
		}
		$plugin = $this->plugins [$pid];
		$rst = apply_filter ( 'uninstall_plugin_' . $pid, true );
		if ($rst) {
			unset ( $this->plugins [$pid] );
			$rst = $this->savePluginData ();
		}
		return $rst;
	}
	/**
	 * 设置插件的不可能状态
	 *
	 * @param string $pid        	
	 * @param int $disabled        	
	 * @return boolean
	 */
	public function setDisabled($pid, $disabled) {
		if (! isset ( $this->plugins [$pid] )) {
			log_debug ( 'PID 为' . $pid . '的插件还未安装或不存在。' );
			return false;
		}
		$this->plugins [$pid] ['disabled'] = $disabled;
		$rst = $this->savePluginData ();
		return $rst;
	}
	/**
	 * 取插件系统
	 *
	 * @param boolean $installed        	
	 * @return ArrayObject
	 */
	public function getPlugins($installed = true) {
		static $scaned = false;
		if (! $scaned) {
			$scaned = true;
			$this->scanPlugins ();
		}
		return $installed ? $this->installed : $this->uninstalled;
	}
	
	// 加载插件信息
	private function loadPlugin($plugin_file) {
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
		if (preg_match ( '/Option\s*:\s+(.*)/', $content, $Option )) {
			$plugin ['Option'] = trim ( $Option [1] );
		} else {
			$plugin ['Option'] = '';
		}
		$plugin ['Plugin'] = str_replace ( array (
				APP_PATH,
				DS 
		), array (
				'',
				'/' 
		), $plugin_file );
		if (isset ( $this->plugins [$plugin ['Plugin_ID']] )) {
			$plugin ['Installed'] = true;
			$plugin ['disabled'] = $this->plugins [$plugin ['Plugin_ID']] ['disabled'];
			$plugin ['Installed_Time'] = $this->plugins [$plugin ['Plugin_ID']] ['installed'];
			$this->installed [$plugin ['Plugin_ID']] = $plugin;
		} else {
			$plugin ['Installed'] = false;
			$plugin ['disabled'] = 1;
			$plugin ['Installed_Time'] = 0;
			$this->uninstalled [$plugin ['Plugin_ID']] = $plugin;
		}
		return $plugin;
	}
}