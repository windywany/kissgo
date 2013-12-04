<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="the content  manager" />
<meta name="author" content="KissGO! group" />
<title>Log in Administrator Controll Panel</title>
<link href="{'metroui/css/metro-bootstrap.css'|assets}"	rel="stylesheet" />
<link href="{'css/login.css'|here}"	rel="stylesheet" />
</head>
<body class="metro">
	<div id="loginWin" class="window flat shadow hide" >
        <div class="caption">
            <span class="icon  icon-enter"></span>
            <div class="title">登录管理后台</div>         
        </div>
        <div class="content">
        	<form action="{$admincp}"  method="POST"  id="login-form">
        		<input type="hidden" name="formid"  value="{$formid}"/>
           		<div class="grid fluid">
           			<div class="row">
           				<label class="span2" for="name">账户:</label>
           				<div data-role="input-control" class="input-control text span9">
                  			<input type="text" name="name"  id="name" tabindex="1"/>
                    	</div>
           			</div>
           			<div class="row">
           				<label class="span2" for="password">密码:</label>
           				<div data-role="input-control" class="input-control text span9">
                  			<input type="password" name="password"  id="password" tabindex="2"/>
                    	</div>
                    	<div class="span1">
                    		<a href="#" title="忘记密码?点此找回.">?</a>
                    	</div>
           			</div>
           			<div class="row">
           				<label class="span9 fg-red" for="name" id="errorMsg"></label>
           				<div data-role="input-control" class="input-control text span2"  id="btn-wrap">
                  			<button class="success"  tabindex="3">登录</button>
                    	</div>
           			</div>
           		</div>
            </form>
        </div>
	</div>
	<script type="text/javascript" src="{'js/sea.js'|assets}"></script>
	<script type="text/javascript" src="{'jquery/jquery-2.0.3.min.js'|assets}"></script>
	<script type="text/javascript" src="{'metroui/metro.min.js'|assets}"></script>
	<script type="text/javascript" src="{'js/comm.js'|assets}"></script>
	<script type="text/javascript">
    seajs.config({
    	vars:{
			locale:'zh_CN',
			assets:'{$assetsurl}'
        },
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