{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages Editor'|ts}{/block}

{block name="breadcrumb" nocache}
<li>{'Pages Editor'|ts}</li>
{/block}

{block name="toolbar"}
    <a data-name="s" class="btn btn-mini btn-success btn-save"><i class="icon-ok-circle"></i> {'Save'|ts}</a>
    <a data-name="sc" class="btn btn-mini btn-primary btn-save-close"><i class="icon-check"></i> {'Save & Close'|ts}</a>    
    <a href="{'admin'|murl:'pages'}" class="btn btn-mini btn-warning"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
{/block}

{block name="admincp_body"}
<form action="{'admin'|murl:pages}" method="POST" id="node-form">
    <input type="hidden" name="type" value="{$type}"/>
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
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#page-widgets" href="#collapseNavi">导航</a>
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
        <a data-name="sc" class="btn btn-primary btn-save-close"><i class="icon-check"></i> {'Save & Close'|ts}</a>    
        <a href="{'admin'|murl:'pages'}" class="btn btn-warning"><i class="icon-refresh"></i> {'Cancel'|ts}</a>
    </div>
</form>
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'editor.js'|here}"></script>
{/block}