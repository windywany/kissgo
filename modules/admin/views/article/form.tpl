{extends file=$ksg_admincp_layout}
{block name="title"}{'Articles'|ts}{/block}
{block name="admincp_css_block"}
<link rel="stylesheet" href="{'ueditor/themes/default/css/ueditor.css'|static}"/>
{/block}   
{block name="toolbar"}
    <a class="btn btn-mini btn-success btn-save" href="#"><i class="icon-ok-circle"></i> {'Save'|ts}</a>
    <a class="btn btn-mini btn-warning" href="{$articleURL}"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
{/block}

{block name="breadcrumb" nocache}
<li><a href="{'admin'|murl:'article'}">{'Articles'|ts}</a><span class="divider">/</span></li>
<li>{$crumb_title}</li>
{/block}

{block name="admincp_body"}

<ul class="nav nav-tabs mgb5">
    <li>&nbsp;&nbsp;</li>
    <li><a href="{$articleURL}"><i class="icon-file"></i> 草稿箱</a></li>    
    <li><a href="{$articleURL}/published" class="tgre"><i class="icon-check"></i> 已发布</a></li>        
    <li class="active"><a href="#"><i class="{$formIcon}"></i> {$crumb_title}</a></li>
</ul>
<form id="article-form" action="{$articleURL}/save/{$article.aid}" method="POST">	
	<div class="well mgb5">
    	<div class="row-fluid">
            <div class="span12">
                <div>
    				<span class="strong">文章标题</span>	
    				<span class="txt-info">[必须填写]</span>
    				<br class="clear"/>								
    			</div>
                <input type="text" id="title" class="title1" name="title" placeholder="请填写文章标题" value="{$article.title}"/>
            </div>                
        </div>
        
        <div class="row-fluid">
        	<div class="span12">
                <div>
    				<span class="strong">内容摘要</span>	
    				<span class="txt-info">[<a href="#" id="btn-show-summary">show</a>]</span>			
    				<br class="clear"/>								
    			</div>
    			<div id="quicktags-wrap" class="hide quicktags">
    				<textarea name="summary" id="summary" rows="3" class="quicktags-editor span12">{$article.summary}</textarea>
    			</div>
            </div>
        </div>
    </div>
    <script type="text/plain" id="myEditor">{$article.body}</script>    
    <div style="text-align:center;padding:5px">
        <a class="btn btn-success btn-save"><i class="icon-ok-circle"></i> {'Save'|ts}</a>	            
        <a class="btn btn-warning" href="{$articleURL}"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
    </div>	
</form>
{/block}

{block name="admincp_foot_js_block"}
{'ueditor/config.js,ueditor/editor.js'|js:misc}
<script type="text/javascript" src="{'form.js'|here}"></script>
{/block}