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
KissGo::startSession ();
$steps = array ('welcome' => 'Welcome', 'check' => 'Environment Check', 'db' => 'Configurate Database', 'admin' => 'Create Administrator', 'config' => 'Configurate', 'done' => 'Done', 'table' => 'table', 'save' => 'save', 'cu' => 'cu' );
$step = $_GET ['step'];
$step = in_array ( $step, array_keys ( $steps ) ) ? $step : 'welcome';
$data = array ('_KISSGO_VERSION' => KissGoSetting::getSetting ( 'VERSION' ) );
$data ['page_title'] = $steps [$step];
$data ['base_url'] = BASE_URL;
$tpl = template ( 'kissgo/install/welcome.tpl', $data );
echo $tpl->render ();
?>