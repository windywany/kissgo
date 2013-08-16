{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages Editor'|ts}{/block}

{block name="breadcrumb" nocache}
<li>{'Pages Editor'|ts}</li>
{/block}

{block name="admincp_body"}

<div class="clearfix" id="overlay-titlebar">
    <div class="clearfix" id="overlay-title-wrapper">
      <h1 id="overlay-title">People</h1>
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
	    
	    <div class="accordion" id="page-widgets">        
	        
	        <div class="accordion-group">
	            <div class="accordion-heading">
	                <a class="accordion-toggle" data-toggle="collapse" data-parent="#page-widgets" href="#collapseBase">基本</a>
	            </div>
	            <div id="collapseBase" class="accordion-body collapse in">
	              <div class="accordion-inner">
	              Anim pariatur cliche...
	              </div>
	            </div>
	        </div>
	        
	        <div class="accordion-group">
	            <div class="accordion-heading">
	                <a class="accordion-toggle" data-toggle="collapse" data-parent="#page-widgets" href="#collapseNavi">导航菜单</a>
	            </div>
	            <div id="collapseNavi" class="accordion-body collapse">
	              <div class="accordion-inner">
	              Anim pariatur cliche...
	              </div>
	            </div>
	        </div>  
	             
	        <div class="accordion-group">
	            <div class="accordion-heading">
	                <a class="accordion-toggle" data-toggle="collapse" data-parent="#page-widgets" href="#collapseSEO">SEO(搜索优化)</a>
	            </div>
	            <div id="collapseSEO" class="accordion-body collapse">
	              <div class="accordion-inner">
	              Anim pariatur cliche...
	              </div>
	            </div>
	        </div>
	        
	        {$widgets}
	        
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