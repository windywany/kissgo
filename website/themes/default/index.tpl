<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{'site_name'|cfg} -- Powered by KissGo! {$_KISSGO_VERSION}</title>
		{'bootstrap.css,bootstrap-responsive.css,common.css'|css:'misc/css'}
        {'jquery/jquery.js,bootstrap/bootstrap.js'|js:misc}				
	</head>
	<body>	
    <div id="wrap">      
      <div class="container">
        <div class="page-header">
          <h1>恭喜, It works!</h1>
        </div>
        <p class="lead">请修改{$_current_template_file}模板文件以自定义此页面。</p>
      </div>
      <div id="push"></div>
    </div>    
	</body>
</html>