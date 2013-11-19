<?php
/*
 * the entry of web pages
 */
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
include_once WEB_ROOT . 'includes/bootstrap.php';

$view = template ( 'a.tpl' );
echo $view->render ();