<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="KissGO! group">
    <title>{block name="title"}KissGO!{/block}</title>
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" href="/static/css/kissgo.css"/>
{block name="css_block"}{/block}
    <script type="text/javascript" language="javascript" src="/static/js/jquery-1.8.x.js"></script>
    <script type="text/javascript" language="javascript" src="/static/bootstrap/bootstrap.js"></script>
    <script type="text/javascript" language="javascript" src="/static/js/kissgo.js"></script>
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
                <li><a href="{$admincp_url}">Home</a><span class="divider">/</span></li>
                {block name="breadcrumb"}{/block}
            </ul>
        </div>
    </div>
</div>