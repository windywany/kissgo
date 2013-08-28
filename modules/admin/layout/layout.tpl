<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content=""/>
    <meta name="author" content="KissGO! group"/>
    <title>{block name="title"}{'Dashboard'|ts}{/block} -- Powered by KissGO! {$_ksg_version}</title>    
    {'bootstrap.css,bootstrap-modal.css,bootstrap-fileupload.css,bootstrap-responsive.css,datepicker.css,select2.css,jquery-ui.css,prettyPhoto.css,ztree.css,common.css'|css:'misc/css'}    
    <link rel="stylesheet" href="{'css/kissgo.css'|here}"/>
    {block name="admincp_css_block"}{/block}    
    {'jquery.js,jquery-ui.js,placeholder.js,validate.js,validate_addons.js,ztree.js,prettyPhoto.js,nestedSortable.js,form.js'|js:'misc/jquery'}
    {'bootstrap.js,modalmanager.js,modal.js,datepicker.js,fileupload.js,select2.js'|js:'misc/bootstrap'}
    {'quicktags.js,common.js'|js:misc}	
    <script type="text/javascript">
    $(function() {
    	$('#btn_goto_top').click(function() {
    		$(window).scrollTop(0);
    		return false;
    	});
    	$(window).scroll(function(e) {
    		if ($(this).scrollTop() > 40) {
    			$('#sideTools').show();
    		} else {
    			$('#sideTools').hide();
    		}
    	});
    	$('.dropdown-submenu > .dropdown-toggle').click(function() {
    		var href = $(this).attr('href');
    		if (href != '#') {
    			window.location.href = href;
    		}
    	});
    });
    </script>
    {block name="admincp_head_js_block"}{/block}
    {'admincp_header'|fire}    
</head>
<body {if $hideNavi}class="nonavibar"{/if}>
<div class="page-containerx">
<!-- head -->
{if !$hideNavi}
<div id="navbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a target="_blank" href="{$_ksg_base_url}" class="brand">{'site_name'|cfg}<i class="icon-share"></i></a>
            <div class="nav-collapse">
                <ul id="menu" class="nav">
                    {$ksg_top_navigation_menu|render}                  
                </ul>
                <ul class="nav pull-right">
                	<li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="icon-plus"></i> {'New'|ts}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            {'add_new_menu_items'|fire}
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="icon-user"></i> {$_ksg_passport['name']}({$_ksg_passport['account']})<span class="caret"></span>
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
        <div class="container-fluid container-fluid-r25">
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
{/if}
<!-- container -->
<div id="container">
    <div id="body" class="container-fluid container-fluid-r25">    
    {if $_ksg_page_tip_info}
        <div class="alert {$_ksg_page_tip_info_cls}">
            <button class="close" data-dismiss="alert">×</button>
            {$_ksg_page_tip_info}
        </div>
    {/if}
    {block name="admincp_body"}{/block}
    </div>
</div>
{if !$hideNavi}
<div id="sideTools">
    <a href="#top" id="btn_goto_top"></a>	
</div>
<!-- foot -->
<div id="foot" class="navbar navbar-fixed-bottom hidden-phone">
    <div class="btn-toolbar">
        <div class="btn-group"><a href="{$_ksg_base_url}" target="_blank"><i class="icon-globe"></i>{'View'|ts}</a></div>
    	{$ksg_foot_toolbar_btns|render}
        <div class="btn-group"><a href="{'admin'|murl:'logout'}"><i class="icon-off"></i>{'Logout'|ts}</a></div>
        <div class="btn-group pull-right">
            <p>&copy; <a href="http://www.kissgo.org/" target="_blank">KissGO! {$_ksg_version}</a> 2012</p>
        </div>
    </div>
</div>
{/if}
</div>
{block name="admincp_foot_js_block"}{/block}
{'admincp_footer'|fire}
</body>
</html>