<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 *
 * $Id$
 */
//------------------------------------------------------------------------
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
require_once WEB_ROOT . 'includes/bootstrap.php';
$req = Request::getInstance ( true );
$op = $req ['__op'];
fire ( 'do_ajax_' . $op, $req );
?>