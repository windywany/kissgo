<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package application
 *
 * $Id$
 */
$settings = KissGoSetting::getSetting ();

define ( 'DEBUG', DEBUG_DEBUG );
define ( 'CLEAN_URL', true );
define ( 'I18N_ENABLED', true );
// 安全码，用于cookie等内容的加密与解密
define ( "SECURITY_KEY", 'yeN3g9EbNfiaZfodV63dI1j8Fbk5HaL7W6yaW4y7u2j4Mf45mPg2v899g451k576' );
// end of file application/bootstrap.php