{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages Editor'|ts}{/block}
{block name="admincp_css_block"}
<link rel="stylesheet" href="{'bootstrap/select2/select2.css'|static}"/>
{/block}
{block name="breadcrumb" nocache}
<li>{'Pages Editor'|ts}</li>
{/block}

{block name="toolbar"}
    <button id="btn-save" data-name="s" value="1" class="btn btn-mini btn-success"><i class="icon-ok-circle"></i> {'Save'|ts}</button>
    <button id="btn-save-close" data-name="sc" value="1" class="btn btn-mini btn-primary"><i class="icon-check"></i> {'Save & Close'|ts}</button>    
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
        {$widgets}        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#page-widgets" href="#collapseSEO">SEO</a>
            </div>
            <div id="collapseSEO" class="accordion-body collapse">
              <div class="accordion-inner">
              Anim pariatur cliche...
              </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#page-widgets" href="#collapseAdvanced">高级</a>
            </div>
            <div id="collapseAdvanced" class="accordion-body collapse">
              <div class="accordion-inner">
                Anim pariatur cliche...
              </div>
            </div>
        </div>
    </div>
</form>
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'bootstrap/select2/select2.min.js'|static}"></script>
<script type="text/javascript" src="{'editor.js'|here}"></script>
{/block}