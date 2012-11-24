<?php
defined('KISSGO') or exit('No direct script access allowed');

$settings = KissGoSetting::getSetting();


$settings[DATABASE] = array(
    'default' => array(
        'driver' => 'PdoMysql',
        'encoding' => 'UTF8',
        'prefix' => '',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'pconnect' => false,
        'dbname' => 'centims'
    )
    //,'another'=>array(), others can be here
);

$settings[INSTALLED_MODULES] = array(
    '::kissgo', '::passport'
);
$settings[INSTALLED_PLUGINS] = array(
    '::hello.php'
);
$settings['site_name'] = ' DEMO';
//end of file settings.php