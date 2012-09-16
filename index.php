<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 *
 * $Id$
 */
//------------------------------------------------------------------------
// the abstract path of web root
define('WEB_ROOT', dirname(__FILE__));
// the default application path
define('APP_PATH', WEB_ROOT . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
// the default kissgo path
define('KISSGO', WEB_ROOT . DIRECTORY_SEPARATOR . 'kissgo' . DIRECTORY_SEPARATOR);

require_once  KISSGO . 'bootstrap.php';
// end of file index.php