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