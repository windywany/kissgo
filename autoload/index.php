<?php
/*
 * the entry of web static page
 */
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
include_once WEB_ROOT . 'includes/bootstrap.php';
$router = Router::getRouter();
$router->cmsDispatch();
?>