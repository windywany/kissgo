<?php
defined('KISSGO') or exit('No direct script access allowed');

$settings = KissGoSetting::getSetting();


$settings[DATABASE]['default'] = array();

$settings[INSTALLED_APPS] = array(
    'aaaa'
);
$settings[INSTALLED_PLUGINS] = array(
    '::hello.php'
);

//end of file settings.php