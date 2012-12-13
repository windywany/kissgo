<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

$settings = KissGoSetting::getSetting ();

$settings ['VERSION'] = '0.1';

$settings ['RELEASE'] = 'BETA';

$settings ['BUILD'] = '10-1206-12';
// end of version.php