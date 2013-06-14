<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{'site_name'|cfg} -- {$title}</title>
		<link href="{'bootstrap/css/bootstrap.css'|static}" rel="stylesheet"/>
		<link href="{'bootstrap/css/bootstrap-responsive.css'|static}" rel="stylesheet"/>
		<script src="{'jquery/jquery.js'|static}"></script>	
		<script src="{'bootstrap/bootstrap.js'|static}"></script>		
	</head>
	<body>	
    <div id="wrap">      
      <div class="container">
        <div class="page-header">
          <h1>{$title}-{$subtitle}</h1>
          <h4>创建者:{$create_user.username}，作者：<a href="{$author|url:author}">{$author.tag}</a>，来源：<a href="{$source|url:source}">{$source.tag}</a></h4>
          <h4>页面类型:{$node_type.name}</h4>
        </div>
        <h1>标签:</h1>
        <ul>
        	{foreach $tags as $tag}
        	<li><a href="{$tag|url:tag}">{$tag.tag}</a></li>
        	{/foreach}
        </ul>
        <h1>标记:</h1>
        <ul>
        	{foreach $flags as $tag}
        	<li><a href="{$tag|url:flag}">{$tag.tag}</a></li>
        	{/foreach}
        </ul>
        <p class="lead">请修改{$_current_template_file}模板文件以自定义此页面。</p>
      </div>
      <div id="push"></div>
    </div>    
	</body>
</html>