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
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
$_kissgo_processing_installation = true;
require_once WEB_ROOT . 'includes/bootstrap.php';
if (is_file ( APPDATA_PATH . 'settings.php' )) {
    Response::redirect ( BASE_URL );
}
require_once KISSGO . 'libs/install.php';
KissGo::startSession ();
$steps = array ('welcome' => 'Welcome', 'profile' => 'Select Install Profile', 'check' => 'Environment Check', 'db' => 'Configurate Database', 'admin' => 'Create Administrator', 'config' => 'Configurate', 'install' => 'Install', 'done' => 'Done', 'cu' => 'cu', 'cm' => 'cm', 'save' => 'save', 'tasks' => 'tasks' );
$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : $_SESSION ['INSTALL_STEP'];
$step = in_array ( $step, array_keys ( $steps ) ) ? $step : 'welcome';

$data = array ('_KISSGO_VERSION' => KISSGO_VERSION . ' BUILD ' . KISSGO_BUILD );
$data ['page_title'] = $steps [$step];
$data ['base_url'] = BASE_URL;
$data ['step'] = $step;
$installer = new KissGOInstaller ();
switch ($step) {
    case 'profile' :
        $_SESSION ['INSTALL_STEP'] = 'profile';
        $data ['profile'] = sess_get ( 'INSTALL_PROFILE' );
        $data ['profiles'] = ProfileManager::getProfile ();
        $tpl = view ( 'admin/views/install/profile.tpl', $data );
        break;
    case 'check' :
        $_SESSION ['INSTALL_STEP'] = 'check';
        if(isset($_POST['from'])){
            $_SESSION ['INSTALL_PROFILE'] = $_POST ['profile'];
        }
        $data ['dirs'] = $installer->check_directory_rw ();
        $data ['envs'] = $installer->check_server_env ();
        $tpl = view ( 'admin/views/install/check.tpl', $data );
        break;
    case 'db' :
        $_SESSION ['INSTALL_STEP'] = 'db';
        $form_data = sess_get ( '_INSTALL_DB_DATA', array () );
        $form = new InstallDbForm ( $form_data );
        $data ['form'] = $form;
        $tpl = view ( 'admin/views/install/db.tpl', $data );
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
        $tpl = view ( 'admin/views/install/admin.tpl', $data );
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
        $profile = ProfileManager::getInstallProfile ();
        $profile->onInitConfigForm ( $form );
        $data ['form'] = $form;
        $tpl = view ( 'admin/views/install/config.tpl', $data );
        break;
    case 'install' :
        $_SESSION ['INSTALL_STEP'] = 'install';
        if (isset ( $_POST ['from'] )) {
            $config_from = new InstallConfigForm ();
            $profile = ProfileManager::getInstallProfile ();
            $profile->onInitConfigForm ( $form );
            $_SESSION ['_INSTALL_CONFIG_DATA'] = $config_from->getCleanData ();
            $config_from->validate ();
            $_SESSION ['_INSTALL_CONFIG_FORM'] = $config_from;
        }
        $data ['db_form'] = $_SESSION ['_INSTALL_DB_FORM'];
        $data ['admin_form'] = $_SESSION ['_INSTALL_ADMIN_FORM'];
        $data ['config_form'] = $_SESSION ['_INSTALL_CONFIG_FORM'];
        if ($data ['db_form']->isValid ()) {
            $data ['db_connection'] = $installer->check_connection ( $_SESSION ['_INSTALL_DB_DATA'] );
            $data ['db_error'] = $installer->error;
        }
        
        $tpl = view ( 'admin/views/install/install.tpl', $data );
        break;
    case 'done' :
        $_SESSION ['INSTALL_STEP'] = 'done';
        if (! $_SESSION ['_INSTALL_CONFIG_FORM'] ['clean_url']) {
            $data ['base_url'] = BASE_URL . 'index.php/';
        }
        $tpl = view ( 'admin/views/install/done.tpl', $data );
        break;
    case 'cu' : // create administrator		
        $rst = $installer->create_administrator ();
        $tpl = new JsonView ( array ('success' => $rst, 'msg' => $installer->error ) );
        break;
    case 'cm' :
        if (isset ( $_POST ['arg'] ) && ! empty ( $_POST ['arg'] )) {
            $rst = $installer->install_module ( $_POST ['arg'] );
            $tpl = new JsonView ( array ('success' => $rst, 'msg' => $installer->error ) );
        } else {
            $tpl = new JsonView ( array ('success' => true, 'taskes' => $installer->get_modules () ) );
        }
        break;
    case 'save' : // save configuration to settings.php file
        $rst = $installer->create_settings_file ();        
        $tpl = new JsonView ( array ('success' => $rst, 'msg' => $installer->error ) );
        $_SESSION ['INSTALL_STEP'] = 'welcome';
        break;
    case 'tasks' : // install task list	        
        $taskes = $installer->get_install_taskes ();
        $tpl = new JsonView ( array ('success' => true, 'taskes' => $taskes ) );
        break;
    case 'welcome':
        $_SESSION ['INSTALL_STEP'] = 'welcome';
        $tpl = view ( 'admin/views/install/welcome.tpl', $data );
        break;
    default :
        exit('ok');
}
echo $tpl->render ();
?>