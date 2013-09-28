<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{$page_title} -- Powered by KissGo! {$_KISSGO_VERSION}</title>
		<link href="{'css/bootstrap.css'|static}" rel="stylesheet"/>
		<link href="{'css/bootstrap-responsive.css'|static}" rel="stylesheet"/>
		<link href="{'css/common.css'|static}" rel="stylesheet"/>
		<link href="{'install.css'|here}" rel="stylesheet"/>	
		<script src="{'jquery/jquery.js'|static}"></script>	
		<script src="{'bootstrap/bootstrap.js'|static}"></script>
		<script src="{'jquery/validate.js'|static}"></script>
		<script src="{'jquery/validate_addons.js'|static}"></script>
		<script src="{'common.js'|static}"></script>        
	</head>
	<body data-spy="scroll" data-target=".sidebar">
		<div class="jumbotron subhead">
		  <div class="container">
		    <h1>KissGO!安装向导</h1>
		    <p class="lead">欢迎使用开源的KissGO!做为您的建站工具和二次开发平台,更多信息请访问<a href="http://www.kissgo.org/">KissGO!</a>官方网站.</p>
		    <p class="lead">本向导将引导您完成KissGO!的安装.</p>
		  </div>
		</div>
		<div class="container">
			<div class="row">
				<div class="span3 sidebar">
			        <ul class="nav nav-list sidenav">
			          <li {if $step=='welcome'}class="active"{/if}><a><i class="icon-chevron-right"></i>安装协议</a></li>
			          <li {if $step=='profile'}class="active"{/if}><a><i class="icon-chevron-right"></i>安装类型</a></li>
			          <li {if $step=='check'}class="active"{/if}><a><i class="icon-chevron-right"></i>环境检测</a></li>
			          <li {if $step=='db'}class="active"{/if}><a><i class="icon-chevron-right"></i>数据库配置</a></li>
			          <li {if $step=='admin'}class="active"{/if}><a><i class="icon-chevron-right"></i>创建管理员</a></li>
			          <li {if $step=='config'}class="active"{/if}><a><i class="icon-chevron-right"></i>基本配置</a></li>
			          <li {if $step=='install'}class="active"{/if}><a><i class="icon-chevron-right"></i>安装信息</a></li>			          
			          <li {if $step=='done'}class="active"{/if}><a><i class="icon-chevron-right"></i>安装</a></li>
			        </ul>
			    </div>
			    <div class="span9">
					<div>
						<div class="page-header">
				        	<h2>{block name="title"}安装协议{/block}</h2>
				       	</div>											
				       	{block name="body"}
				       	<div class="well" style="height:350px;overflow-y:auto;">
				       		{include file="$ksg_module/admin/views/install/license.tpl"}
				       	</div>
				       	<form class="form-inline pull-right" onsubmit="check" method="post">
				       		<input type="hidden" name="step" value="profile"/>						   
						    <label class="checkbox">
						    	<input type="checkbox" id="accept"/>我已经阅读并同意此协议
						    </label>
						    <button type="submit" class="btn btn-primary" id="start-btn" disabled="disabled">继续</button>
						</form>
						<script type="text/javascript">
							function check(){							
								return $('#accept').attr('checked');
							}
							$(function(){
								$('#accept').change(function(){
									if($(this).attr('checked')){
										$('#start-btn').removeAttr('disabled');
									}else{
										$('#start-btn').attr('disabled','disabled');
									}
								});
							});
						</script>
				       	{/block}
					</div>
			    </div>
			</div>
		</div>
		<script type="text/javascript">
			$(function(){
				$window = $(window);
				$('.sidenav').affix();
			});
		</script>	
	</body>
</html>
