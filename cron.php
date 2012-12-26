<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 *
 * $Id$
 */
//------------------------------------------------------------------------
require_once dirname ( __FILE__ ) . '/bootstrap.php';
// mybe this script only be executed locally.
fire ( 'crontab' );
//end of cron.php