<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-10-10 下午1:13
 * $Id$
 */
$__kissgo_exports[] = KISSGO . 'core';
$__kissgo_exports[] = KISSGO . 'core/ds';
$__kissgo_exports[] = KISSGO . 'core/rs';
$__kissgo_exports[] = KISSGO . 'vendors/smarty';
if (is_dir(APP_PATH . 'includes')) {
    $__kissgo_exports[] = APP_PATH . 'includes';
}
// END OF FILE path.php