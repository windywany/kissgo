<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="KissGO! group">
        <title>{block name="title"} 404 Not Found {/block}-- Powered by KissGO! {$_ksg_rversion}</title>
        {'bootstrap.css,bootstrap-responsive.css,common.css'|css:'misc/css'}
        {'jquery/jquery.js,bootstrap/bootstrap.js'|js:misc}
    </head>
    <body>
    <div id="xui-messagebox" class="modal" style="top:10px;box-shadow:none;border:none;">      
      <div class="modal-body" style="text-align: center; max-height:460px !important;">
      	<img alt="404 not found" style="width: 500px;height:454px;" src="{'images/404.png'|static}"/>
      </div>
      <div class="modal-footer" style="padding: 5px 15px 5px;background:none;border:none;box-shadow:none;">
        <a class="btn" href="{$_ksg_base_url}">GO HOME</a>
      </div>
    </div>
    </body>
</html>