<?php
/*
 * the entry of administrator panel
 */
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
include_once WEB_ROOT . 'includes/bootstrap.php';
$router = Router::getRouter ();
$router->route ( 'admin' );
?>