<?php /* Smarty version Smarty-3.1.12, created on 2013-11-25 16:28:24
         compiled from "D:\www\kissgo\autoload\modules\admin\views\login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2558852930a2897f0b1-25975938%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '53c8a25ced8a99838919cf3acb495863310ae6f5' => 
    array (
      0 => 'D:\\www\\kissgo\\autoload\\modules\\admin\\views\\login.tpl',
      1 => 1385367997,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2558852930a2897f0b1-25975938',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'siteurl' => 0,
    'moduledir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52930a289ad0a6_36722532',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52930a289ad0a6_36722532')) {function content_52930a289ad0a6_36722532($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="the content  manager" />
<meta name="author" content="KissGO! group" />
<title>Log in Administrator Controll Panel</title>
<link href="<?php echo ASSETS_URL.'metroui/css/metro-bootstrap.css';?>
"	rel="stylesheet" />
</head>
<body class="metro">
	<div id="loginWin" class="window hide">
        <div class="caption">
            <span class="icon icon-windows"></span>
            <div class="title">登录</div>         
        </div>
        <div class="content">
            Window content
        </div>
	</div>
	<script type="text/javascript" src="<?php echo ASSETS_URL.'js/sea.js';?>
"></script>
	<script type="text/javascript" src="<?php echo ASSETS_URL.'jquery/jquery-2.0.3.min.js';?>
"></script>
	<script type="text/javascript" src="<?php echo ASSETS_URL.'metroui/metro.min.js';?>
"></script>	
	<script type="text/javascript">
    seajs.config({
        base: '<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
<?php echo $_smarty_tpl->tpl_vars['moduledir']->value;?>
/',         
        map:[
        	['.js','.js?20131121001']
      ]
    });    
    seajs.use(['admin/js/login'], function(login) { 
        $(function(){
       	 	login.main();        
        });           
    });
    </script>
</body>
</html><?php }} ?>