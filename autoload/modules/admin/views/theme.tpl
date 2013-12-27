{extends file=$layout}
{block name="layout_style_block"}
	<link href="{'jquery/ztree/zTreeStyle.css'|module}"	rel="stylesheet" />
{/block}
{block name="subtitle"}主题模板{/block}
{block name="workbench"}
<h3>本系统共有{$totalTheme}个主题, 当前使用<em id="theme-indicator">{$current_theme}</em>主题.</h3>

<div class="accordion with-marker" data-role="accordion">
	{foreach $themes as $key=>$theme}
    <div class="accordion-frame">
        <a href="#" class="heading {if $key==$current_theme}active bg-green fg-white{/if}">{$key}</a>
        <div class="content">
				<h4>模板设置</h4>
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                            	<th class="text-left w200">页面类型</th>
                            	<th class="text-left w250">模板文件</th>
                            	<th class="text-left wa">说明</th>
                            	<th class="text-left w100"></th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach $theme as $item}
                        	<tr>
                        		<td>{$item[0]}</td>
                        		<td id="{$key}-{$item@key}-tpl">{$item[1]}</td>
                        		<td> {$item[3]}</td>
                        		<td>
                        		    <a href="#{$item@key}" class="edit-tpl" data-theme="{$key}" data-type="{$item@key}"><i class="icon-edit"></i> 设置模板</a>
                        		</td>
                        	</tr>
                    	{/foreach}
                    	</tbody>
                    </table>
             	 	<button class="button  primary use-this-tpl {if $current_theme == $key}hide{/if}"  data-theme="{$key}">使用此主题</button>
              		<button class="button warning reset-all-tpl" data-theme="{$key}">重置所有模板</button>
        </div>
    </div>
    {/foreach}
</div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['admin/js/theme','jquery/blockit','jquery/ztree/core'], function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}
