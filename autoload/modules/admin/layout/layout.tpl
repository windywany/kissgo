<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="the content  manager" />
        <meta name="author" content="KissGO! group" />
        <title>{block name="title"}Administrator Controll Panel{/block} - Kissgo!</title>
        <link href="{'metroui/css/metro-bootstrap.css'|assets}"	rel="stylesheet" />
        <link href="{'css/comm.css'|assets}"	rel="stylesheet" />
        {block name="layout_style_block"}{/block}
        <script type="text/javascript" src="{'js/sea.js'|assets}"></script>
    	<script type="text/javascript" src="{'jquery/jquery-2.0.3.min.js'|assets}"></script>
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
        </script>
    </head>
    <body class="metro">        
	    <div class="navigation-bar dark fixed-top">
            <div class="navigation-bar-content" id="top-menu">
                <div class="element">
                		<a class="dropdown-toggle"><i class="icon-home"></i>我的网站</a>
                		<ul class="dropdown-menu" data-role="dropdown">
                				<li><a href="{$siteurl}" target="_blank">访问网站</a></li>                                    
                		</ul>
                </div>
                <span class="element-divider"></span>
                <div class="element">
                	<a class="dropdown-toggle"><span class="icon-glasses-2"></span>页面</a>
                	<ul data-role="dropdown" class="dropdown-menu" >
                        <li><a href="#">页面</a></li>
                        <li><a href="#">文章</a></li>
                        <li><a href="#">图片</a></li>                                
                    </ul>
                </div>
                <div class="element input-element" id="top-search-wrap">
                 	<div class="input-control text" >
							<input type="text" placeholder="Search..." />
							<button class="btn-search"></button>
                  	</div>
           		 </div>               		 
                <a href="{$admincp}?logout" class="element place-right"><i class="icon-exit fg-red"  id="btn-exit"></i></a>                
                <span class="element-divider place-right"></span>
                <div class="element place-right">
                            <a class="dropdown-toggle">
                                <span class="icon-cog"></span>
                            </a>
                            <ul data-role="dropdown" class="dropdown-menu place-right" >
                            	<li><a href="{$admincp}?clear" ><span class="icon-remove fg-red" ></span>清空运行时缓存</a></li>                                
                            </ul>
				</div>
                <span class="element-divider place-right"></span>
                <a class="element place-right"  href="#"><i class="icon-user"></i> Welcome: Administrator</a> 
            </div>                		
        </div>
        <div id="workspace" > 
        	<h2><a href="{$admincp}" id="goto-start-screen"><i class="icon-arrow-left-3 fg-darker "></i></a> <span id="app-title">{block name="subtitle"}开始{/block}</span></h2>
            <div id="workbench">
            	{block name="workbench"}{/block}
            </div>          
        </div> 
        {block name="layout_foot_block"}{/block}
    </body>
</html>