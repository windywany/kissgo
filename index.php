<?php
/**
 *
 * Id: $ID$
 */
//------------------------------------------------------------------------
if (ini_get('register_globals')) {
    exit ("Error: register_globals must be set to Off in php.ini");
}
// the abstract path of web root
define('WEB_ROOT', dirname(__FILE__));

define('APP_DIR', 'application');
define('KISSGO', 'kissgo');

require_once  WEB_ROOT . DIRECTORY_SEPARATOR . KISSGO . DIRECTORY_SEPARATOR . 'bootstrap.php';
// end of file index.php