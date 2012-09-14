<?php
/**
 * $Id$
 */
defined('KISSGO') or exit('No direct script access allowed');

if (version_compare('5.2', phpversion(), '>')) {
    die (sprintf('Your php version is %s,but kissgo required  php 5.2+', phpversion()));
}

set_error_handler('_exception_handler');

if (version_compare(phpversion(), '5.3', '<')) {
    @set_magic_quotes_runtime(0);
}

@ini_set('magic_quotes_sybase', 0);

define('DS', DIRECTORY_SEPARATOR);
// the default application name, this is used by session id
defined('APP_NAME') or define('APP_NAME', basename(WEB_ROOT));
// the default application path
defined('APP_PATH') or define('APP_PATH', WEB_ROOT . DS . APP_DIR . DS);
// the default kissgo path
defined('KISSGO_PATH') or define('KISSGO_PATH', WEB_ROOT . DS . KISSGO . DS);
// the default apps path
defined('APPS') or define('APPS', APP_PATH . DS . 'apps' . DS);

$ng_config_file = APP_PATH . 'conf/settings.php';
if (isset($_SERVER['a'])) {

} else {

}

include $ng_config_file;
// end of file bootstrap.php