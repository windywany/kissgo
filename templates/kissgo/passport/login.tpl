<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{'Login'|ts}[Powered by KissGo! {$_KISSGO_R_VERSION}]</title>
		<link href="{'bootstrap/css/bootstrap.css'|static}" rel="stylesheet"/>
		<link href="{'bootstrap/css/bootstrap-responsive.css'|static}" rel="stylesheet"/>
		<link href="{'common.css'|static}" rel="stylesheet"/>
		<link href="{'../css/login.css'|here}" rel="stylesheet"/>
	</head>
	<body>
		<div id="login-wrap">
			<div class="title">
				<h1>登录</h1>
			</div>
			<div id="login-body">
				<form id="login-form" class="well form-inline" action="." method="post">
					<div id="eMsg" class="alert alert-error {if $form->isValid()}hide{/if}">
						<button class="close">×</button>
						<div id="eMsgdiv">{$form|form:errors}</div>
					</div>
					<div>
						<div class="input-prepend  pull-left">
							<span class="add-on"><i class="icon-user"></i></span>
                            <input class="w120" id="account" type="text" name="account" value="{$form|form:value:account}"/>
			          	</div>
			          	<div class="input-prepend input-append pull-right">
							<span class="add-on"><i class="icon-lock"></i></span>
                            <input class="w90" id="passwd" type="password" name="passwd" value="{$form|form:value:passwd}"/>
                            <span class="add-on" title="忘记密码?"><i class="icon-question-sign"></i></span>
			          	</div>
			          	<br class="clear"/>
		          	</div>
		          	
		          	<div class="rows">
		          		{if $captcha}
		          		<div class="pull-left">
			          		<div class="input-prepend input-append">
								<span class="add-on"><i class="icon-picture"></i></span><input class="w120" id=captcha type="text" name="captcha"/><span class="add-on"><img title="看清不清,点击切换." id="captcha-img" src="{'captcha'|murl}?size=50x20"/></span>
				          	</div>				          	
			          	</div>
			          	{/if}		          		
		          		<button type="submit" class="btn btn-primary pull-right"><i class="icon-user icon-white"></i>登录</button>
		          		<br class="clear"/>
		          	</div>
				</form>
			</div>
			<div class="info">本系统基于<a href="http://www.kissgo.org/">KissGo! {$_KISSGO_VERSION}</a>构建,由<a href="http://www.kissgo.org/">KissGo!</a>提供动力。</div>
		</div>	
		<script type="text/javascript" src="{'jquery/jquery.js'|static}"></script>
		<script type="text/javascript" src="{'bootstrap/bootstrap.js'|static}"></script>
		<script type="text/javascript">
			$(function(){
				var imgSrc = $('#captcha-img').attr('src');
				$('#captcha-img').click(function(){ 
					$(this).attr('src',imgSrc+'&_t='+(new Date().getTime()));
				});				
				$('#login-form').submit(function(){ 
					var captcha = $('#captcha'),account = $('#account'),passwd = $('#passwd');
					if($.trim(account.val()).length == 0){
						$('#eMsgdiv').html('请输入账户');
						$('#eMsg').show();
						return false;
					} 
					if($.trim(passwd.val()).length == 0){
						$('#eMsgdiv').html('请输入密码');
						$('#eMsg').show();
						return false;
					} 
					if(captcha.length > 0 && $.trim(captcha.val()).length < 4){
						$('#eMsgdiv').html('请输入正确的验证码');
						$('#eMsg').show();
						return false;
					} 
					return true;
				});
				$('.close').click(function(){					
					$('#eMsg').hide();
					return false;
				});
			});
		</script>
	</body>
</html>
