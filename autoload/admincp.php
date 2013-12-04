<?php
/*
 * the entry of administrator panel
 */
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
include_once WEB_ROOT . 'includes/bootstrap.php';
if (isset ( $_SERVER ['PATH_INFO'] )) {
    $url = trim ( $_SERVER ['PATH_INFO'], '/' );
} else {
    $url = 'admin';
}
$router = Router::getRouter ();
$router->route ( $url );
?>