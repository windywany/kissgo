<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-10-10 下午1:56
 * $Id$
 */
imports('aaaa/forms/*', 'aaaa/models/*');
function dox_aaaa_index($req, $res) {

}

$uM = new CoreUserModel();

$users = $uM->retrieve();

return new SmartyView('test/index.tpl', array('name' => 'Leo'));
// END OF FILE index.php