{extends file=$ksg_admincp_layout}
{block name="title"}{'Theme'|ts}{/block}
{block name="breadcrumb" nocache}
<li>{'Theme'|ts}</li>
{/block}
{block name="admincp_body"}
<h4 class="text-info">共有{$theme_count}个主题，当前使用<em id="theme-indicator">{$current_theme}</em>主题。</h4>
<div class="accordion" id="themes" data-current-url="{$_CUR_URL}">
    {foreach $themes as $theme}
    <div class="accordion-group" id="theme-wrapper-{$theme@key}">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" data-parent="#themes" href="#theme-{$theme@key}">
             <span class="label {if $current_theme == $theme@key}label-success{/if}">{$theme@key}</span>
          </a>
        </div>
        <div id="theme-{$theme@key}" data-theme-id="{$theme@key}" class="accordion-body collapse {if $current_theme == $theme@key}in{/if}">
          <div class="accordion-inner">
                    <h4>模板设置</h4>
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                            	<th>页面类型</th>
                            	<th>模板文件</th>
                            	<th></th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach $theme as $item}
                        	<tr>
                        		<td class="w150">{$item.name}</td>
                        		<td class="wa" id="{$theme@key}-{$item.type}-tpl">
                        		{if $item.tpl}
                        		    {$item.tpl}
                        		{else}
                        		    {$item.template}
                        		{/if}
                        		</td>
                        		<td class="w100">
                        		    <a href="#{$item.type}" class="edit-tpl"><i class="icon-edit"></i> 设置模板</a>
                        		</td>
                        	</tr>
                    	{/foreach}
                    	</tbody>
                    </table>              
              <button class="btn use-this-tpl {if $current_theme == $theme@key}hide{/if}" style="margin-right:15px">使用此主题</button>
              
              <button class="btn btn-danger reset-all-tpl">重置所有模板</button>
          </div>
        </div>
    </div>
    {/foreach}
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
<script type="text/javascript" src="{'theme.js'|here}"></script>
{/block}