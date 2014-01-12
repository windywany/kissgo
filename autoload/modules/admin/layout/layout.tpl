<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="Kissgo! Administrator Dashboard" />
        <meta name="author" content="KissGO! group" />
        <title>{block name="title"}Administrator Controll Panel{/block} - Kissgo!</title>
        <link href="{'metroui/css/metro-bootstrap.css'|assets}"	rel="stylesheet" />
        <link href="{'jquery/ui/css/jquery-ui.min.css'|assets}"	rel="stylesheet" />
        <link href="{'css/comm.css'|assets}"	rel="stylesheet" />
        {block name="layout_style_block"}{/block}
        <script type="text/javascript" src="{'js/sea.js'|assets}"></script>
    	<script type="text/javascript" src="{'jquery/jquery-2.0.3.min.js'|assets}"></script>
    	<script type="text/javascript" src="{'jquery/ui/jquery-ui.min.js'|assets}"></script>
    	<script type="text/javascript" src="{'metroui/metro.min.js'|assets}"></script>
    	<script type="text/javascript" src="{'js/comm.js'|assets}"></script>
    	<script type="text/javascript">
        seajs.config({
            vars:{
				locale:'zh_CN',
				assets:'{$assetsurl}',
				admincp:'{$admincp}'
            },
            base: '{$moduleurl}',
            map:[
            	['.js','.js?1.0']
          ]
        });
        KsgApp.base = '{$siteurl}';
        </script>
    </head>
    <body class="metro">
	    <div class="navigation-bar dark fixed-top">
            <div class="navigation-bar-content" id="top-menu">
                <a class="element" href="{$siteurl}" target="_blank"><i class="icon-home"></i>我的网站</a>
                <span class="element-divider"></span>
                <a href="{$admincp}?logout" class="element place-right"><i class="icon-exit fg-red"  id="btn-exit"></i></a>
                <span class="element-divider place-right"></span>
                <div class="element place-right">
                            <a class="dropdown-toggle" href="#">
                                <i class="icon-cog"></i>
                            </a>
                            <ul data-role="dropdown" class="dropdown-menu place-right" >
                            	<li class="divider"></li>
                            	<li><a href="{$admincp}/admin/settings/" ><span class="icon-tools" ></span>系统设置</a></li>
                            	<li><a href="{$admincp}?clear" ><span class="icon-remove fg-red" ></span>清空运行时缓存</a></li>
                            </ul>
				</div>
                <span class="element-divider place-right"></span>
                <a class="element place-right"  href="{$admincp}/admin/profile/"><i class="icon-user"></i>{$passport->getName()}</a>
                <div class="element input-element place-right" id="top-search-wrap">
                	<form action="{$admincp}/nodes/" method="get">
                     	<div class="input-control text" >
    							<input type="text" placeholder="Search..."  name="key"/>
    							<button class="btn-search" id="g-btn-search"></button>
                      	</div>
                  	</form>
           		 </div>
            </div>
        </div>
        <div id="workspace" >
        	<h2 id="title-bar">
        		<a href="{$admincp}" id="goto-start-screen"><span class="icon-arrow-left-3 fg-darker "></span></a>
        		{block name="subtitle"}开始{/block}
        		{block name="toolbar"} {/block}
        	</h2>
        	<div id="msgbox" style="display:none;"></div>
            <div id="workbench">
            	{block name="workbench"}{/block}
            </div>
        </div>
        {block name="layout_foot_block"}{/block}
    </body>
</html>