{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages Editor'|ts}{/block}
{block name="admincp_css_block"}
    <style style="text/css">
        #page-flags li {
            display: block;
            float: left;
            overflow: hidden;
            width: 70px;
        }
        .form-field span.tag {
            margin: 2px;
            overflow: hidden;
            width: 60px;
        }
        #menu {
	         background-color: #FFF;
            cursor: pointer;
        }
    </style>
{/block} 
{block name="breadcrumb" nocache}
<li>{'Pages Editor'|ts}</li>
{/block}

{block name="admincp_body"}

<div class="clearfix" id="overlay-titlebar">
    <div class="clearfix" id="overlay-title-wrapper">
      <h1 id="overlay-title">{'Publish'|ts} - 普通文章 [110]</h1>
    </div>
    <div id="overlay-close-wrapper">
      <a class="overlay-close" href="#" id="overlay-close"></a>
    </div>    
</div>


<div id="overlay-content" class="clearfix">
	<div class="overlay-body">
	<form action="{'admin'|murl:pages}" method="POST" id="node-form">
	    <input type="hidden" name="type" value="{$type}"/>
	    <input type="hidden" name="node_id" value="{$node_id}"/>
	    <div class="row-fluid">
	        <div class="span8">
	            <div>
    				<span class="strong">标题</span>	
    				<span class="txt-error"></span>
    				<br class="clear"/>								
    			</div>
	            <input type="text" class="title1" name="title" placeholder="页面标题"/>
	        </div>
	        <div class="span4">
	            <div>
    				<span class="strong">副标题</span>	
    				<span class="txt-error"></span>
    				<br class="clear"/>								
    			</div>
	            <input type="text" class="title1" name="subtitle" placeholder="页面副标题"/>
	        </div>
	    </div>
	    
	    
        <div class="row-fluid">
			<div>
				<span class="strong">URL</span>										
				<span class="txt-info">[以http://开头的URL将自动跳转.]</span>
				<span class="txt-error"></span>
				<br class="clear"/>								
			</div>
			<input type="text" value="2.html" class="span12" id="url" name="url"/>
		</div>
	   
	   <div class="row-fluid">
			<div>
				<span class="strong">添加至导航菜单</span>
				<br class="clear"/>								
			</div>
			<input type="text" value="" readonly="readonly" class="span12" id="menu" name="menu" placeholder="点击选择导航菜单"/>
		</div>
	    
	    <div class="row-fluid">
			<div>
				<span class="strong">关键词</span>										
				<span class="txt-info">[如果不填写，将使用全局定义的关键词，多个关键词以(,)分隔]</span>	
				<a href="#" id="select-keywords" class="pull-right mg-r5">常用关键词</a>
				<br class="clear"/>								
			</div>
			<input type="text" value="" placeholder="在此键入关键词" class="span12" id="keywords" name="keywords"/>
		</div>
	    
	    <div class="row-fluid">
			<div>
				<span class="strong">页面描述</span>										
				<span class="txt-info">[如果不填写，将使用全局定义的描述]</span>									
				<br class="clear"/>								
			</div>
			<textarea rows="2" id="descripition" placeholder="在此键入页面描述" name="descripition" class="span12"></textarea>
		</div>
	    
	    <div class="row-fluid">
	        <label class="strong">内容摘要</label>			
			<textarea rows="3" id="summary" placeholder="在此键入页面描述" name="summary" class="span12"></textarea>
		</div>
	    
	    <div class="vertical-tabs clearfix">
	    	<ul class="vertical-tabs-list">
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#page-options">
	    				<strong>发布选项</strong>
	    				<span class="summary"></span>
	    			</a>
	    		</li>
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#page-tags">
	    				<strong>标签与属性</strong>
	    				<span class="summary"></span>
	    			</a>
	    		</li>
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#page-author">
	    				<strong>作者与来源</strong>
	    				<span class="summary"></span>
	    			</a>
	    		</li>	    		
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#page-image">
	    				<strong>页面插图</strong>
	    				<span class="summary">无插图</span>
	    			</a>
	    		</li>	    		
	    	</ul>
	    	<div class="vertical-tabs-panes vertical-tabs-processed">
	    	    <fieldset id="page-options" class="vertical-tabs-pane">
					<div class="fieldset-wrapper">
    					<div class="form-field">
    						<div>
    							<span>模板</span>
    							<label class="checkbox pull-left mg-r5"><input name="custome_tpl_chk" id="custom-set-tpl" type="checkbox">自定义</label>
    							<br class="clear">								
    						</div>
    						<div style="display: none;" class="input-append hide" id="tpl-wrapper">
    			                <input class="w180" id="template_file" name="template_file" value="" readonly="readonly" type="text"><button class="btn" type="button" id="btn-select-tpl">选择..</button>
    			            </div>
    					</div>
    					<div class="form-field">
    						<label>置顶到</label>																		
    			            <input class="w180 hasDatepicker" id="ontopto" name="ontopto" placeholder="默认不置顶" value="" type="text">
    					</div>
    					<div class="form-field">
    						<label>缓存时间</label>												  									
    			            <input class="w80 mg-r5" style="float:left;" id="cachetime" name="cachetime" value="0" type="text">
    			            <span class="txt-info info">0表示不缓存，单位秒.</span>
    			            <br class="clear">
    					</div>
    					<div class="form-field">
    						<label class="checkbox"><input name="commentable" id="commentable" type="checkbox">允许评论</label>
    					</div>
					</div>
				</fieldset>	    		
				<fieldset id="page-tags" class="vertical-tabs-pane">
					<div class="fieldset-wrapper">
    					<div class="form-field">
    						<label>属性</label>
    						<ul id="page-flags">
    						    <li><label class="checkbox"><input type="checkbox" value="2" name="flags[]">小样</label></li>
    						</ul>
    						<br class="clear"/>
    					</div>
    					<div class="form-field">
    						<div>
    							<label class="pull-left mg-r5">标签</label>
    							<span class="txt-info">多个标签以(,)分隔.</span>
    							<br class="clear">								
    						</div>
    						<div class="input-append">		  									
    				        	<input type="text" id="tags" class="w250"><button id="btn-add-tag" type="button" class="btn">添加</button><button id="btn-select-tag" type="button" class="btn">常用</button>
    				        </div>								        
    					</div>
    					<div id="page-tags" class="form-field">
    					    <span class="tag label"><input type="hidden" value="dsfasdf" name="tags[]"><i class="icon-trash"></i>dsfasdf</span>																			
    						<br class="clear">
    					</div>
					</div>
				</fieldset>
				<fieldset id="page-author" class="vertical-tabs-pane">
					<div class="fieldset-wrapper">
    					<div class="form-field">
    						<label>作者</label>
    						<div class="input-append">		  									
    			                <input class="w250" id="author" name="author" value="" type="text"/><button class="btn btn-enums" type="button" data-for="author" title="作者">选择..</button>
    			            </div>
    					</div>
    					<div class="form-field">
    						<label>来源</label>
    						<div class="input-append">		  									
    			                <input class="w250" id="source" name="source" value="" type="text"/><button class="btn btn-enums" type="button" data-for="source" title="来源">选择..</button>
    			            </div>
    					</div>
					</div>
				</fieldset>
				
				<fieldset id="page-image" class="vertical-tabs-pane">
					<div class="fieldset-wrapper"></div>
				</fieldset>
			</div>
		</div>
	    
	    
	    <div class="form-actions" style="text-align:center;padding:10px">
	        <a data-name="s" class="btn btn-success btn-save"><i class="icon-ok-circle"></i> {'Save'|ts}</a>	            
	        <a href="#" class="btn btn-warning overlay-close"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
	    </div>
	</form>
	</div>
</div>
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'editor.js'|here}"></script>
{/block}