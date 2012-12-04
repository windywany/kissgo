<?php
/*
 * Install application
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
$_kissgo_processing_installation = true;
require_once dirname ( __FILE__ ) . '/bootstrap.php';
if (is_file ( APPDATA_PATH . 'settings.php' )) {
	Response::redirect ( BASE_URL );
}
require_once APP_PATH . 'version.php';
require_once KISSGO . 'libs/install.php';
KissGo::startSession ();
$steps = array ('welcome' => 'Welcome', 'check' => 'Environment Check', 'db' => 'Configurate Database', 'admin' => 'Create Administrator', 'config' => 'Configurate', 'install' => 'Install', 'done' => 'Done', 'scheme' => 'scheme', 'cu' => 'cu', 'save' => 'save', 'tasks' => 'tasks' );
$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : $_SESSION ['INSTALL_STEP'];
$step = in_array ( $step, array_keys ( $steps ) ) ? $step : 'welcome';
$data = array ('_KISSGO_VERSION' => KissGoSetting::getSetting ( 'VERSION' ) );
$data ['page_title'] = $steps [$step];
$data ['base_url'] = BASE_URL;
$data ['step'] = $step;
switch ($step) {
	case 'check' :
		$_SESSION ['INSTALL_STEP'] = 'check';
		$tpl = template ( 'kissgo/install/check.tpl', $data );
		break;
	case 'db' :
		$_SESSION ['INSTALL_STEP'] = 'db';
		$tpl = template ( 'kissgo/install/db.tpl', $data );
		break;
	case 'admin' :
		$_SESSION ['INSTALL_STEP'] = 'admin';
		$tpl = template ( 'kissgo/install/admin.tpl', $data );
		break;
	case 'config' :
		$_SESSION ['INSTALL_STEP'] = 'config';
		$tpl = template ( 'kissgo/install/config.tpl', $data );
		break;
	case 'install' :
		$_SESSION ['INSTALL_STEP'] = 'install';
		$tpl = template ( 'kissgo/install/install.tpl', $data );
		break;
	case 'done' :
		$_SESSION ['INSTALL_STEP'] = 'done';
		$tpl = template ( 'kissgo/install/done.tpl', $data );
		break;
	case 'scheme' : // create the scheme of kissgo	
		sleep(3);	
		if(isset($_POST['arg'])&&!empty($_POST['arg'])){			
			$tpl = new JsonView ( array ('success' => true ) );
		}else{
			$tpl = new JsonView ( array ('success' => true, 'taskes' => get_scheme_tables() ) );
		}
		break;
	case 'cu' : // create administrator		
		sleep(3);
		$tpl = new JsonView ( array ('success' => true ) );
		break;
	case 'save' : // save configuration to settings.php file
		sleep(3);
		$tpl = new JsonView ( array ('success' => true ) );
		break;
	case 'tasks' : // install task list	
		sleep(3);	
		$taskes = get_install_taskes ();
		$tpl = new JsonView ( array ('success' => true, 'taskes' => $taskes ) );
		break;
	default :
		$_SESSION ['INSTALL_STEP'] = 'welcome';
		$tpl = template ( 'kissgo/install/welcome.tpl', $data );
}
echo $tpl->render ();
?>