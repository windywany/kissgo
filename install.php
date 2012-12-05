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
$steps = array (
                'welcome' => 'Welcome', 
                'check' => 'Environment Check', 
                'db' => 'Configurate Database', 
                'admin' => 'Create Administrator', 
                'config' => 'Configurate', 
                'install' => 'Install', 
                'done' => 'Done', 
                'scheme' => 'scheme', 
                'cu' => 'cu', 
                'save' => 'save', 
                'tasks' => 'tasks' 
);
$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : ''/*$_SESSION ['INSTALL_STEP']*/;
$step = in_array ( $step, array_keys ( $steps ) ) ? $step : 'welcome';
$data = array (
            '_KISSGO_VERSION' => KissGoSetting::getSetting ( 'VERSION' ) 
);
$data ['page_title'] = $steps [$step];
$data ['base_url'] = BASE_URL;
$data ['step'] = $step;
switch ($step) {
    case 'check' :
        $_SESSION ['INSTALL_STEP'] = 'check';
        $data ['dirs'] = check_directory_rw ();
        $data ['envs'] = check_server_env ();
        $tpl = template ( 'kissgo/install/check.tpl', $data );
        break;
    case 'db' :
        $_SESSION ['INSTALL_STEP'] = 'db';
        $form_data = sess_get ( '_INSTALL_DB_DATA', array () );
        $form = new InstallDbForm ( $form_data );
        $data ['form'] = $form;
        $tpl = template ( 'kissgo/install/db.tpl', $data );
        break;
    case 'admin' :
        $_SESSION ['INSTALL_STEP'] = 'admin';
        if (isset ( $_POST ['from'] )) {
            $db_from = new InstallDbForm ();
            $_SESSION ['_INSTALL_DB_DATA'] = $db_from->getCleanData ();
            $db_from->validate ();
            $_SESSION ['_INSTALL_DB_FORM'] = $db_from;
        }
        $form_data = sess_get ( '_INSTALL_ADMIN_DATA', array () );
        $form = new InstallAdminForm ( $form_data );
        $data ['form'] = $form;
        $tpl = template ( 'kissgo/install/admin.tpl', $data );
        break;
    case 'config' :
        $_SESSION ['INSTALL_STEP'] = 'config';
        if (isset ( $_POST ['from'] )) {
            $admin_from = new InstallAdminForm ();
            $_SESSION ['_INSTALL_ADMIN_DATA'] = $admin_from->getCleanData ();
            $admin_from->validate ();
            $_SESSION ['_INSTALL_ADMIN_FORM'] = $admin_from;
        }
        $form_data = sess_get ( '_INSTALL_CONFIG_DATA', array () );
        $form = new InstallConfigForm ( $form_data );
        $data ['form'] = $form;
        $tpl = template ( 'kissgo/install/config.tpl', $data );
        break;
    case 'install' :
        $_SESSION ['INSTALL_STEP'] = 'install';
        if (isset ( $_POST ['from'] )) {
            $config_from = new InstallConfigForm ();
            $_SESSION ['_INSTALL_CONFIG_DATA'] = $config_from->getCleanData ();
            $config_from->validate ();
            $_SESSION ['_INSTALL_CONFIG_FORM'] = $config_from;
        }
        $data ['db_form'] = $_SESSION ['_INSTALL_DB_FORM'];
        $data ['admin_form'] = $_SESSION ['_INSTALL_ADMIN_FORM'];
        $data ['config_form'] = $_SESSION ['_INSTALL_CONFIG_FORM'];
        if($data ['db_form']->isValid()){
            $data ['db_connection'] = $data ['db_form']->check_connection ( $_SESSION ['_INSTALL_DB_DATA'] );
            $data ['db_error'] = DataSource::getLastError ();
        }
        $tpl = template ( 'kissgo/install/install.tpl', $data );
        break;
    case 'done' :
        $_SESSION ['INSTALL_STEP'] = 'done';
        $tpl = template ( 'kissgo/install/done.tpl', $data );
        break;
    case 'scheme' : // create the scheme of kissgo	
        sleep ( 2 );
        if (isset ( $_POST ['arg'] ) && ! empty ( $_POST ['arg'] )) {
            $tpl = new JsonView ( array (
                                        'success' => true 
            ) );
        } else {
            $tpl = new JsonView ( array (
                                        'success' => true, 
                                        'taskes' => get_scheme_tables () 
            ) );
        }
        break;
    case 'cu' : // create administrator		
        sleep ( 2 );
        $tpl = new JsonView ( array (
                                    'success' => true 
        ) );
        break;
    case 'save' : // save configuration to settings.php file
        sleep ( 2 );
        $tpl = new JsonView ( array (
                                    'success' => true 
        ) );
        break;
    case 'tasks' : // install task list	
        sleep ( 2 );
        $taskes = get_install_taskes ();
        $tpl = new JsonView ( array (
                                    'success' => true, 
                                    'taskes' => $taskes 
        ) );
        break;
    case 'checkmysql' :
        $tpl = new JsonView ( array (
                                    'success' => true 
        ) );
        break;
    default :
        $_SESSION ['INSTALL_STEP'] = 'welcome';
        $tpl = template ( 'kissgo/install/welcome.tpl', $data );
}
echo $tpl->render ();
?>