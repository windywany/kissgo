<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="KissGO! group">
    <title>{block name="title"}KissGO!{/block}</title>
    <link rel="stylesheet" href="{'bootstrap/css/bootstrap.css'|static}"/>
    <link rel="stylesheet" href="{'bootstrap/css/bootstrap-responsive.css'|static}"/>
    <link rel="stylesheet" href="{'common.css'|static}"/>
    <link rel="stylesheet" href="{'css/kissgo.css'|here}"/>
    {block name="css_block"}{/block}
    <script type="text/javascript" language="javascript" src="{'js/jquery-1.8.x.js'|static}"></script>
    <script type="text/javascript" language="javascript" src="{'bootstrap/bootstrap.js'|static}"></script>
    <script type="text/javascript" language="javascript" src="{'js/kissgo.js'|here}"></script>
    {'kissgo_dashboard_header'|fire}
    {block name="head_js_block"}{/block}
</head>
<body>
<!-- head -->
<div id="navbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a target="_blank" title="Preview KissGO! 1.0 Demo" href="{$_SITE_URL}" class="brand">KissGO! 1.0 Demo... <i class="icon-share"></i></a>
            <div class="nav-collapse">
                <ul id="menu" class="nav">
                    {$_top_navigation_menu->render()}
                </ul>
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="icon-user"></i>{$_PASSPORT['name']}({$_PASSPORT['account']})<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">Control Panel</a>
                            </li>
                            <li class="divider"><span></span></li>
                            <li><a href="{'passport'|murl:'logout'}"><i class="icon-off"></i>{'Logout'|ts}</a></li>
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
                <li><a href="{$admincp_url}">{'Dashboard Home'|ts}</a><span class="divider">/</span></li>
                {block name="breadcrumb"}{/block}
            </ul>
        </div>
    </div>
</div>