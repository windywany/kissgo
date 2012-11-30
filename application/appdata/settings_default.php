<?php
// configuration file, don't edit this file manually, The KissGO! provides a interface to manage this.
defined('KISSGO') or exit('No direct script access allowed');
$settings = KissGoSetting::getSetting();

$settings['DEBUG'] = 2;

$settings['CLEAN_URL'] = true;

$settings['I18N_ENABLED'] = true;

$settings['GZIP_ENABLED'] = true;

$settings['TIMEZONE'] = 'Asia/Shanghai';

$settings[DATABASE] = array(
    'default' => array(
        'driver' => 'PdoMysql',
        'encoding' => 'UTF8',
        'prefix' => '',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'pconnect' => false,
        'dbname' => 'kissgodb'
    )
);
$settings['SECURITY_KEY'] = 'yeN3g9EbNfiaZfodV63dI1j8Fbk5HaL7W6yaW4y7u2j4Mf45mPg2v899g451k576';
//end of file settings.php