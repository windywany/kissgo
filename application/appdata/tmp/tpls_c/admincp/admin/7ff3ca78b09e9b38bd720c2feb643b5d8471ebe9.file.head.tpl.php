<?php /* Smarty version Smarty-3.1.12, created on 2012-11-21 17:25:34
         compiled from "C:\java sources\kissgo\templates\admincp\head.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2919750ac9e0eee2fe4-47081226%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7ff3ca78b09e9b38bd720c2feb643b5d8471ebe9' => 
    array (
      0 => 'C:\\java sources\\kissgo\\templates\\admincp\\head.tpl',
      1 => 1353378939,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2919750ac9e0eee2fe4-47081226',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_SITE_URL' => 0,
    '_top_navigation_menu' => 0,
    'admincp_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50ac9e0ef0faa5_15742407',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ac9e0ef0faa5_15742407')) {function content_50ac9e0ef0faa5_15742407($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="KissGO! group">
    <title>KissGO!</title>
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" href="/static/css/kissgo.css"/>

    <script type="text/javascript" language="javascript" src="/static/js/jquery-1.8.x.js"></script>
    <script type="text/javascript" language="javascript" src="/static/bootstrap/bootstrap.js"></script>
    <script type="text/javascript" language="javascript" src="/static/js/kissgo.js"></script>

</head>
<body>
<!-- head -->
<div id="navbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a target="_blank" title="Preview KissGO! 1.0 Demo" href="<?php echo $_smarty_tpl->tpl_vars['_SITE_URL']->value;?>
" class="brand">KissGO! 1.0 Demo... <i class="icon-share"></i></a>
            <div class="nav-collapse">
                <ul id="menu" class="nav">
                    <?php echo $_smarty_tpl->tpl_vars['_top_navigation_menu']->value->render();?>

                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">System<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Control Panel</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Global Configuration</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Global Check-in</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Clear Cache</a></li>
                            <li><a href="#">Purge Expired Cache</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">System Information</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">System<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">Control Panel</a>
                            </li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Global Configuration</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Global Check-in</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Clear Cache</a></li>
                            <li><a href="#">Purge Expired Cache</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">System Information</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="icon-user"></i>Leo Ning<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">Control Panel</a>
                            </li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Global Configuration</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Global Check-in</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">Clear Cache</a></li>
                            <li><a href="#">Purge Expired Cache</a></li>
                            <li class="divider"><span></span></li>
                            <li><a href="#">System Information</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumbs -->
<div id="crumb">
    <div class="container-fluid">
        <div class="row-fluid">
            <ul class="breadcrumb">
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['admincp_url']->value;?>
">Home</a><span class="divider">/</span></li>
                
            </ul>
        </div>
    </div>
</div><?php }} ?>