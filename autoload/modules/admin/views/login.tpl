<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="the content  manager" />
<meta name="author" content="KissGO! group" />
<title>Log in Administrator Controll Panel</title>
</head>
<body>
	<div>login</div>
	<script type="text/javascript" src="{'js/sea.js'|assets}"></script>	
	<script type="text/javascript">
    seajs.config({
        base: '{$siteurl}{$moduledir}/',       
        map:[
        	['.js','.js?20131121001']
      ]
    });    
    seajs.use('admin/js/login', function(login) {
       login.main();
    });
    </script>
</body>
</html>