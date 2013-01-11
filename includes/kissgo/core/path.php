<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-10-10 下午1:13
 * $Id$
 */
$__kissgo_exports [] = KISSGO . 'core';
$__kissgo_exports [] = KISSGO . 'core/dao';
$__kissgo_exports [] = KISSGO . 'core/ds';
$__kissgo_exports [] = KISSGO . 'core/rs';
if (is_dir ( INCLUDES )) {
    $__kissgo_exports [] = INCLUDES . 'classes';
    $__kissgo_exports [] = INCLUDES . 'vendors';
    $__kissgo_exports [] = INCLUDES . 'vendors' . DS . 'smarty';
}
// END OF FILE path.php