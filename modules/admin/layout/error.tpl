<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="KissGO! group">
        <title>{block name="title"}{'Dashboard Home'|ts}{/block} -- Powered by KissGO! {$_ksg_rversion}</title>
        {'bootstrap.css,bootstrap-responsive.css,common.css'|css:'misc/css'}
        {'jquery/jquery.js,bootstrap/bootstrap.js'|js:misc}	 
    </head>
    <body>
    <div id="xui-messagebox" class="modal">
      <div class="modal-header">        
        <h3 class="{$type}">{$title}</h3>
      </div>
      <div class="modal-body {$type}">{$message}</div>
      <div class="modal-footer">
        <a class="btn" href="{$redirect}">确定</a>        
      </div>
    </div>
    </body>
</html>