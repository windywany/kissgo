<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="the content  manager" />
<meta name="author" content="KissGO! group" />
<title>Log in Administrator Controll Panel</title>
<link href="{'metroui/css/metro-bootstrap.css'|assets}"	rel="stylesheet" />
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
    seajs.use(['admin/js/login'], function(login) { 
        $(function(){
       	 	login.main();        
        });           
    });
    </script>
</body>
</html>