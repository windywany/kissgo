<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="the content  manager" />
        <meta name="author" content="KissGO! group" />
        <title>Administrator Controll Panel</title>
        <link href="{'metroui/css/metro-bootstrap.css'|assets}"	rel="stylesheet" />        
        <link href="{'css/dashboard.css'|here}" rel="stylesheet"/>
        {foreach $styles as $style}
        <link href="{$style}"	rel="stylesheet" />
        {/foreach}
        <style type="text/css">{'get_admincp_embed_style'|fire}</style>
    </head>
    <body class="metro">        
	    <div class="navigation-bar dark fixed-top">
            <div class="navigation-bar-content" id="top-menu">
                <div class="element">
                		<a href="#" class="dropdown-toggle"><span class="icon-home"></span>我的网站</a>
                		<ul class="dropdown-menu" data-role="dropdown">
                				<li><a href="{''|base}" target="_blank">访问网站</a></li>                                    
                		</ul>
                </div>
                <span class="element-divider"></span>
                <div class="element">
                	<a href="#" class="dropdown-toggle"><span class="icon-glasses-2"></span>页面</a>
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
                <a href="{$admincp}?logout" class="element place-right"><span class="icon-exit fg-red"  id="btn-exit"></span></a>                
                <span class="element-divider place-right"></span>
                <div class="element place-right">
                            <a href="#" class="dropdown-toggle">
                                <span class="icon-cog"></span>
                            </a>
                            <ul data-role="dropdown" class="dropdown-menu place-right" >
                            	<li><a href="{$admincp}?clear" ><span class="icon-remove fg-red" ></span>清空运行时缓存</a></li>                                
                            </ul>
				</div>
                <span class="element-divider place-right"></span>
                <button class="element place-right" onclick="KsgApp.open('profile/js/app','个人面板')"><span class="icon-user"></span> Welcome: Administrator</button>                    
            </div>                		
        </div>
        <div id="workspace" > 
        	<h2><a href="#" id="goto-start-screen"><i class="icon-arrow-left-3 fg-darker "></i></a> <span id="app-title">开始</span></h2>
            <div id="workbench">
            	<div id="start-screen">
            		<div class="tile-group double">
            			<div class="tile-group-title">内容</div>
            			<div class="tile bg-green" onclick="KsgApp.open('nodes/js/app','页面')">
                            <div class="tile-content icon">
                                <i class="icon-files"></i>
                            </div>
                            <div class="brand">
                                <span class="label fg-white">页面</span>
                                <span class="badge bg-orange">12</span>
                            </div>
                        </div>
                        <div class="tile bg-yellow">
                            <div class="tile-content icon">
                                <i class="icon-comments-4"></i>
                            </div>
                            <div class="brand">
                                <span class="label fg-white">评论</span>
                                <span class="badge bg-orange">12</span>
                            </div>
                        </div>
                        
                        <div class="tile bg-cyan">
                            <div class="tile-content icon">
                                <i class="icon-pictures"></i>
                            </div>
                            <div class="brand">
                                <span class="label fg-white">多媒体</span>
                                <span class="badge bg-orange">12</span>
                            </div>
                        </div>
                        
            		</div>
            		
            		<div class="tile-group">
            			<div class="tile-group-title">外观</div>
            			<div class="tile bg-mauve">
                            <div class="tile-content icon">
                                <i class="icon-newspaper"></i>
                            </div>
                            <div class="brand">
                                <span class="label fg-white">主题-default</span>                                                         
                            </div>
                        </div>
                        <div class="tile bg-darkIndigo">
                            <div class="tile-content icon">
                                <i class="icon-tree-view"></i>
                            </div>
                            <div class="brand">
                                <span class="label fg-white">导航菜单</span>
                            </div>
                        </div>
            		</div>
            		
            	</div>
            	<div id="desktop"  style="display:none;"></div>
            </div>          
        </div>            
    	<script type="text/javascript" src="{'js/sea.js'|assets}"></script>
    	<script type="text/javascript" src="{'jquery/jquery-2.0.3.min.js'|assets}"></script>
    	<script type="text/javascript" src="{'metroui/metro.min.js'|assets}"></script>	
    	<script type="text/javascript">
        seajs.config({
            vars:{
				locale:'zh_CN',
				assets:'{$assetsurl}'
            },
            base: '{$moduleurl}',       
            map:[
            	['.js','.js?1.0']
          ]
        });
        seajs.use(['admin/js/dashboard'], function(dashboard) {
			window.KsgApp = dashboard;
            $(function(){
            	dashboard.main();
            });        	
        });
        </script>
    </body>
</html>