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
return WEB_ROOT.'<br/>'.detect_app_base_url() . "<pre>" . print_r ( $_SERVER, true ) . "<pre>";