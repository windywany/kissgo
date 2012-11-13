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
define('WEB_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
// the default application path
define('APP_PATH', WEB_ROOT . 'application' . DIRECTORY_SEPARATOR);
// the default kissgo path
define('KISSGO', WEB_ROOT . 'kissgo' . DIRECTORY_SEPARATOR);

require_once  KISSGO . 'bootstrap.php';
KissGo::getInstance()->run();
// end of file index.php