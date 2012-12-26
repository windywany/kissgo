<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 *
 * $Id$
 */
//------------------------------------------------------------------------
// if you use the default structure kissgo provide, you need not to change
// below setting.
//------------------------------------------------------------------------
// the abstract path of web root
define ( 'WEB_ROOT', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
// the default application path
define ( 'APP_PATH', WEB_ROOT . 'webapp' . DIRECTORY_SEPARATOR );
define ( 'KISSGO', APP_PATH . 'kissgo' . DIRECTORY_SEPARATOR );
require_once KISSGO . 'bootstrap.php';
// END OF bootstrap.php