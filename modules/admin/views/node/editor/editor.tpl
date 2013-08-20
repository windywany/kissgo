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
        .datepicker span{
			float:none;
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
	<form action="{'admin'|murl:'pages/publish'}" method="POST" id="node-form">
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
				<br class="clear"/>								
			</div>
			<input type="hidden" value="abc,def,gdf" placeholder="在此键入关键词" class="wf txt-select2" id="keywords" name="keywords"/>
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
    						<div class="input-append hide" id="tpl-wrapper">
    			                <input class="w250" id="template_file" name="template_file" value="" readonly="readonly" type="text"/><button class="btn" type="button" id="btn-select-tpl">选择..</button>
    			            </div>
    					</div>
    					<div class="form-field">
    						<label>置顶到</label>
    						<div id="time1" class="input-append date datepicker">
        				    	<input class="w250" id="ontopto" name="ontopto" placeholder="默认不置顶" value="" type="text"/><span class="add-on"><i class="icon-calendar"></i></span>
        				    </div>
    					</div>
    					<div class="form-field">
    						<label>缓存时间</label>												  									
    			            <input class="w80 mg-r5" style="float:left;" id="cachetime" name="cachetime" value="0" type="text"/>
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
    						<div>		  									
    				        	<input type="hidden" id="tags" class="wf txt-select2"/>
    				        </div>								        
    					</div>    					
					</div>
				</fieldset>
				<fieldset id="page-author" class="vertical-tabs-pane">
					<div class="fieldset-wrapper">
    					<div class="form-field">
    						<label>作者</label>
    						<input class="w250" id="author" name="author" value="abc" type="hidden"/>    			            
    					</div>
    					<div class="form-field">
    						<label>来源</label>    						
    						<input class="w250" id="source" name="source" value="def" type="hidden"/>    			           
    					</div>
					</div>
				</fieldset>
				
				<fieldset id="page-image" class="vertical-tabs-pane">
					<div class="fieldset-wrapper"></div>
				</fieldset>
			</div>
		</div>
	    
	    
	    <div class="form-actions" style="text-align:center;padding:10px">
	        <a class="btn btn-success btn-save"><i class="icon-ok-circle"></i> {'Save'|ts}</a>	            
	        <a href="#" class="btn btn-warning overlay-close"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
	    </div>
	</form>
	</div>
</div>

<div class="modal hide fade" tabindex="-1" id="tpl-selector-box" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>选择模板</h3>
    </div>
    <div class="modal-body" style="max-height:300px;overflow:auto;">
        <ul class="ztree" id="tpls-tree"></ul>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="btn-close-form" data-dismiss="modal">关闭</a>
        <a href="#" class="btn btn-primary" id="btn-done">确定</a>
    </div>
</div>
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'editor.js'|here}"></script>
{/block}