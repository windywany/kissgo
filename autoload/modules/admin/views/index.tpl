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
    </head>
    <body class="metro">        
	    <div class="navigation-bar dark fixed-top">
            <div class="navigation-bar-content" id="top-menu">
                <div class="element">
                		<a href="#" class="dropdown-toggle"><span class="icon-home"></span>我的网站</a>
                		<ul class="dropdown-menu" data-role="dropdown">
                				<li><a href="{''|base}">访问网站</a></li>                                    
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
                                <li><a href="#">Products</a></li>
                                <li><a href="#">Download</a></li>
                                <li><a href="#">Support</a></li>
                                <li><a href="#">Buy Now</a></li>
                            </ul>
				</div>
                <span class="element-divider place-right"></span>
                <a href="#" class="element place-right"><span class="icon-user"></span> Welcome: Administrator</a>                    
            </div>                		
        </div>
        <div id="workspace" > 
        	<h2><a href="#"><i class="icon-arrow-left-3 fg-darker "></i></a> <span>开始</span><a href="#" class="place-right"><i class="icon-grid-view"></i></a></h2>
            <div id="workbench">
            	<div id="start-screen">
            		
            		
            		    <div class="tile bg-darkPink">
    <div class="tile-content icon">
    <i class="icon-cart-2"></i>
    </div>
    <div class="tile-status">
    <span class="name">Store</span>
    </div>
    </div>
     
    <div class="tile double bg-amber">
    <div class="tile-content icon">
    <i class="icon-play-alt"></i>
    </div>
    <div class="brand bg-black">
    <span class="label fg-white">Player</span>
    <div class="badge bg-darkRed paused"></div>
    </div>
    </div>
     
    <div class="tile">
    <div class="tile-content image">
    <img src="images/author.jpg">
    </div>
    <div class="brand">
    <span class="label fg-white">Images</span>
    <span class="badge bg-orange">12</span>
    </div>
    </div>
     
    <div class="tile double">
    <div class="tile-content image">
    <img src="images/4.jpg">
    </div>
    <div class="brand bg-dark opacity">
    <span class="text">
    This is a desert eagle. He is very hungry and angry bird.
    </span>
    </div>
    </div>
            		
            		
            	</div>
            	<div id="desktop">
            		
            	</div>
            </div>          
        </div>            
    	<script type="text/javascript" src="{'js/sea.js'|assets}"></script>
    	<script type="text/javascript" src="{'jquery/jquery-2.0.3.min.js'|assets}"></script>
    	<script type="text/javascript" src="{'metroui/metro.min.js'|assets}"></script>	
    	<script type="text/javascript">
        seajs.config({
            base: '{$siteurl}{$moduledir}/',       
            map:[
            	['.js','.js?20131121001']
          ]
        });
        seajs.use('admin/js/dashboard', function(dashboard) {
            $(function(){
            	dashboard.main();
            });        	
        });
        </script>
    </body>
</html>