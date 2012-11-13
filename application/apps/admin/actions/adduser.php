<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Leo
 * Date: 12-11-2
 * Time: 下午7:45
 * To change this template use File | Settings | File Templates.
 */

// $request = Request::getInstance();

$request = Request::getInstance();
return admin_view('admincp/admin/adduser.tpl', array('age' => $request['age']));