<?php
/*
 * simple mvc entry
 * 
 * read the do param and dispatch it to corresponding Controller. 
 */
define ( 'WEB_ROOT', dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR );
include_once WEB_ROOT . 'includes/bootstrap.php';
$route = rqst ( 'do', '' );
if ($route) {
    $router = Router::getRouter ();
    $router->route ( $route );
} else {
    Response::respond ( 404 );
}
?>