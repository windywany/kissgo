<?php
/*
 * Install library
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 *
 * $Id$
 */
/**
 * 
 * 安装任务列表
 * @return array
 */
function get_install_taskes() {
	$taskes = array ();
	$taskes [] = array ('text' => '获取数据表列表', 'step' => 'scheme', 'weight' => 5 );
	$taskes [] = array ('text' => '创建管理员', 'step' => 'cu', 'weight' => 15 );
	$taskes [] = array ('text' => '创建settings.php文件', 'step' => 'save', 'weight' => 15 );
	return $taskes;
}
/**
 * 
 * 得到要安装的表
 */
function get_scheme_tables(){
	$taskes = array ();
	$taskes [] = array ('text' => '创建表:abc', 'step' => 'scheme','arg'=>'abc', 'weight' => 20 );
	$taskes [] = array ('text' => '创建表:def', 'step' => 'scheme','arg'=>'abc', 'weight' => 20 );
	$taskes [] = array ('text' => '创建表:ghi', 'step' => 'scheme','arg'=>'abc', 'weight' => 20 );
	return $taskes;
}
/**
 * 检测目录读写权限
 * @return array
 */
function check_directory_rw(){
	$dirs = array('appdata'=>APPDATA_PATH,'logs'=>APPDATA_PATH.'logs','tmp'=>TMP_PATH);
	$rst = array();
	foreach ($dirs as $dir=>$path){
		$r = is_readable($path);
		$len = @file_put_contents($path.'test.dat', 'test');
		$w = $len > 0;
		$rt = array('dir'=>$dir,'path'=>$path);
		$rx = $r?'<span class="label label-success mr10">可读</span>':'<span class="label label-important">不可读</span>';
		if($w){
			$wx = '<span class="label label-success mr10">可写</span>';
			@unlink($path.'test.dat');
		}else{
			$wx ='<span class="label label-important">不可写</span>';
		}		
		if(!$w || !$r){
			$rt['cls'] = 'error';
		}else{
			$rt['cls'] = 'success';
		}
		$rt['status'] = $rx.$wx;
		$rst[] = $rt;
	}
	return $rst;
}
function check_server_env(){
	$envs = array();
	$env = array();
	$env['name'] = 'PHP';
	$env['requirement'] = '5.2+'; 	
	$env['current'] = phpversion();
	$env['cls'] = version_compare ( '5.2', phpversion (), '<=' )?'success':'error';
	$envs[] = $env;
	//mysql
	$env['name'] = 'MySQL';
	$env['requirement'] = '有';
	if(function_exists('mysql_query')){
		$env['current'] = '<span class="label label-success mr10">有</span>';
		$env['cls'] = 'success';
	}else{
		$env['current'] = '<span class="label label-important">无</span>';
		$env['cls'] = 'error';
	}	
	$envs[] = $env;
	// gd
	$env['name'] = 'GD';
	$env['requirement'] = '有';
	if(extension_loaded('gd')){
		$env['current'] = '<span class="label label-success mr10">有</span>';
		$env['cls'] = 'success';
	}else{
		$env['current'] = '<span class="label label-important">无</span>';
		$env['cls'] = 'error';
	}
	$envs[] = $env;
	// json
	$env['name'] = 'json';
	$env['requirement'] = '有';
	if(function_exists('json_encode')){
		$env['current'] = '<span class="label label-success mr10">有</span>';
		$env['cls'] = 'success';
	}else{
		$env['current'] = '<span class="label label-important">无</span>';
		$env['cls'] = 'error';
	}
	$envs[] = $env;
	// mb_string
	$env['name'] = 'mb_string';
	$env['requirement'] = '可选';
	if(function_exists ( 'mb_internal_encoding' )){
		$env['current'] = '<span class="label label-success mr10">有</span>';
		$env['cls'] = 'success';
	}else{
		$env['current'] = '<span class="label label-important">无</span>';
		$env['cls'] = 'warning';
	}
	$envs[] = $env;
	// pdo_mysql
	$env['name'] = 'pdo_mysql';
	$env['requirement'] = '可选';
	if(extension_loaded('pdo_mysql')){
		$env['current'] = '<span class="label label-success mr10">有</span>';
		$env['cls'] = 'success';
	}else{
		$env['current'] = '<span class="label label-important">无</span>';
		$env['cls'] = 'warning';
	}
	$envs[] = $env;
	return $envs;
}