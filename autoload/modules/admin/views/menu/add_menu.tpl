{extends file=$layout}
{block name="layout_style_block"}
	<link href="{'../css/menu.css'|here}"	rel="stylesheet" />
{/block}
{block name="subtitle"}<a href="{$admincp}/admin/menu/">导航菜单</a> - 新增菜单{/block}
{block name="workbench"}
<form action="{$admincp}/admin/menu/save/" id="menu_form" method="post" class="grid fluid">
    <input type="hidden" name="id" value="0"/>
    <fieldset>
    	<legend>菜单信息</legend>
    	<div class="row">
    			<div class="span2"><label for="name">菜单名</label></div>
    			<div data-role="input-control" class="input-control text span5">
                    <input type="text"  tabindex="1" name="name" id="name"/>
                </div>
    			<div class="span5"><label for="name" class="error tip">任意字符。</label></div>
    	</div>
    	<div class="row">
    			<div class="span2"><label for="alias">引用名</label></div>
    			<div data-role="input-control" class="input-control text span5">
                    <input type="text"  tabindex="2" name="alias" id="alias"/>
                </div>
    			<div class="span5"><label for="alias" class="error tip">只能由字母,数字或下划线组成,模板中可使用的引用名.</label></div>
    	</div>
    </fieldset>
    <div class="row">
		<div class="span2"></div>
		<div class="span5">
		    <a class="button large default" tabindex="8" href="{$admincp}/admin/menu/"><i class="icon-undo on-left"></i>返回</a>
			<button class="button large success" tabindex="7"><i class="icon-floppy on-left"></i>保存</button>
		</div>
	</div>
</form>

{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['admin/js/menu','jquery/form','jquery/blockit','jquery/validate'], function(app) {
            $(function(){
                var validateRule = {$validateRule};
            	app.initAddForm(validateRule);
            });
        });
</script>
{/block}
