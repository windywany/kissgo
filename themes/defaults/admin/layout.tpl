<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content=""/>
    <meta name="author" content="KissGO! group"/>
    <title>{block name="title"}{'Dashboard'|ts}{/block} -- Powered by KissGO! {$ksg_version}</title>
    <link rel="stylesheet" href="{'bootstrap/css/bootstrap.css'|static}"/>
    <link rel="stylesheet" href="{'bootstrap/css/bootstrap-responsive.css'|static}"/>
    <link rel="stylesheet" href="{'common.css'|static}"/>
    <link rel="stylesheet" href="{'css/kissgo.css'|here}"/>
    {block name="admincp_css_block"}{/block}
    <script type="text/javascript" src="{'jquery/jquery.js'|static}"></script>
    <script type="text/javascript" src="{'bootstrap/bootstrap.js'|static}"></script>
    <script type="text/javascript" src="{'jquery/plugins/validate.js'|static}"></script>
	<script type="text/javascript" src="{'jquery/plugins/validate_addons.js'|static}"></script>
	<script type="text/javascript" src="{'common.js'|static}"></script>
    <script type="text/javascript" src="{'js/kissgo.js'|here}"></script>
    {'admincp_header'|fire}
    {block name="admincp_head_js_block"}{/block}
</head>
<body>
<!-- head -->
<div id="navbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a target="_blank" href="{$ksg_site_url}" class="brand">{'site_name'|cfg}<i class="icon-share"></i></a>
            <div class="nav-collapse">
                <ul id="menu" class="nav">
                    {$ksg_top_navigation_menu|render}                    
                </ul>
                <ul class="nav pull-right">
                	<li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="icon-plus"></i> {'Add'|ts}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            {'add_new_menu_items'|fire}
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="icon-user"></i> {$ksg_passport['name']}({$ksg_passport['account']})<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            {'add_passport_menu_items'|fire}
                            <li class="divider"><span></span></li>
                            <li><a href="{'admin'|murl:'logout'}"><i class="icon-off"></i> {'Logout'|ts}</a></li>
                        </ul>
                    </li>
                </ul>                
            </div>
        </div>
    </div>
    <!-- breadcrumbs -->
    <div id="crumb">
        <div class="container-fluid">
            <div class="row-fluid">
                <ul class="breadcrumb">
                    <li><a href="{$ksg_admincp_url}"><i class="icon-home"></i> {'Dashboard'|ts}</a><span class="divider">/</span></li>
                    {block name="breadcrumb"}{/block}
                    <li class="pull-right">
                        {block name="toolbar"}{/block}                        
                    </li>
                </ul>                
            </div>
        </div>
    </div>
</div>

<!-- container -->
<div id="container">
    <div id="body" class="container-fluid">
    {if $_ksg_page_tip_info}
        <div class="alert {$_ksg_page_tip_info_cls}">
            <button class="close" data-dismiss="alert">×</button>
            {$_ksg_page_tip_info}
        </div>
    {/if}
    {block name="admincp_body"}{/block}
    </div>
</div>
<div id="sideTools">
    <a href="#top" id="btn_goto_top"></a>	
</div>
<!-- foot -->
<div id="foot" class="navbar navbar-fixed-bottom hidden-phone">
    <div class="btn-toolbar">
        <div class="btn-group"><a href="{$ksg_site_url}" target="_blank"><i class="icon-globe"></i>{'View'|ts}</a></div>
    	{$ksg_foot_toolbar_btns|render}
        <div class="btn-group"><a href="{'admin'|murl:'logout'}"><i class="icon-off"></i>{'Logout'|ts}</a></div>
        <div class="btn-group pull-right">
            <p>&copy; <a href="http://www.kissgo.org/" target="_blank">KissGO! {$ksg_version}</a> 2012</p>
        </div>
    </div>
</div>
<div id="overlay">
    <div id="overlay-body">
        <img src="{'images/overlay.gif'|here}">
        <div class="msg">处理中...</div>
    </div>
</div>
{'admincp_footer'|fire}
{block name="admincp_foot_js_block"}{/block}
</body>
</html>