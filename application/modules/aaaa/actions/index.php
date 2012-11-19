<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-10-10 下午1:56
 * $Id$
 */
$url = 'abc/aaaa?leo=name';

echo "<pre>";

print_r(parse_url($url));
echo "</pre>";
// END OF FILE index.php