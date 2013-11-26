<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="the content  manager" />
        <meta name="author" content="KissGO! group" />
        <title>Administrator Controll Panel</title>
        <link href="{'metroui/css/metro-bootstrap.css'|assets}"	rel="stylesheet" />
    </head>
    <body>
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
        	dashboard.main();
        });
        </script>
    </body>
</html>