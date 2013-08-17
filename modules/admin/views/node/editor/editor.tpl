{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages Editor'|ts}{/block}

{block name="breadcrumb" nocache}
<li>{'Pages Editor'|ts}</li>
{/block}

{block name="admincp_body"}

<div class="clearfix" id="overlay-titlebar">
    <div class="clearfix" id="overlay-title-wrapper">
      <h1 id="overlay-title">{'Publish'|ts}</h1>
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
	            <input type="text" class="title1" name="title" placeholder="页面标题"/>
	        </div>
	        <div class="span4">
	            <input type="text" class="title1" name="subtitle" placeholder="页面副标题"/>
	        </div>
	    </div>
	    
	    <div class="vertical-tabs clearfix">
	    	<ul class="vertical-tabs-list">
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#abc">
	    				<strong>Menu settings</strong>
	    				<span class="summary">abcaa</span>
	    			</a>
	    		</li>
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#def">
	    				<strong>Revision information</strong>
	    				<span class="summary">No revision</span>
	    			</a>
	    		</li>
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#">
	    				<strong>URL path settings</strong>
	    				<span class="summary">No alias</span>
	    			</a>
	    		</li>
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#">
	    				<strong>Comment settings</strong>
	    				<span class="summary">Open</span>
	    			</a>
	    		</li>
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#">
	    				<strong>Authoring information</strong>
	    				<span class="summary">By admin</span>
	    			</a>
	    		</li>
	    		<li tabindex="-1" class="vertical-tab-button">
	    			<a href="#">
	    				<strong>Publishing options</strong>
	    				<span class="summary">Published, Promoted to front page</span>
	    			</a>
	    		</li>
	    	</ul>
	    	<div class="vertical-tabs-panes vertical-tabs-processed">
	    		<fieldset id="abc" class="vertical-tabs-pane" style="display: block;">	    			
	    			<div class="fieldset-wrapper" style="height:500px"></div>
	    		</fieldset>
				<fieldset id="def" class="vertical-tabs-pane" style="display: none;">
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