{extends file=$layout}
{block name="layout_style_block"}
    <link href="{'jquery/select2/select2.css'|module}"	rel="stylesheet" />
	<link href="{'../css/menu.css'|here}"	rel="stylesheet" />
{/block}
{block name="subtitle"}<a href="{$admincp}/admin/menu/">导航菜单</a> - 编辑菜单{/block}
{block name="workbench"}
<form action="{$admincp}/admin/menu/save/" id="menu_form" method="post" class="grid fluid">
    <input type="hidden" name="id" id="menuid" value="{$menu.id}"/>
    <fieldset>
    	<legend>菜单信息</legend>
    	<div class="row">
        	<div class="span2"><label for="name">菜单名</label></div>
        	<div data-role="input-control" class="input-control text span5">
                <input type="text"  tabindex="1" name="name" id="name" value="{$menu.name}"/>
            </div>
        	<div class="span5"><label for="name" class="error tip">任意字符。</label></div>
    	</div>
    	<div class="row">
    		<div class="span2"><label for="alias">引用名</label></div>
    		<div data-role="input-control" class="input-control text span5">
                <input type="text"  tabindex="2" name="alias" id="alias" value="{$menu.alias}" readonly="readonly"/>
            </div>
    		<div class="span5"><label for="alias" class="error tip">只能由字母,数字或下划线组成,模板中可使用的引用名.</label></div>
    	</div>
    </fieldset>
    <fieldset>
	<legend>菜单项设置</legend>
	<div id="pagestuff">
        <div id="side-info-column" class="xsidebar grid fluid">
            <div class="tab-control" data-role="tab-control">
                <ul class="tabs">

                    <li class="active"><a href="#_page_custom">自定义</a></li>
                    <li><a href="#_page_node">页面</a></li>
                </ul>
                <div class="frames">
                    <div class="frame" id="_page_node">
                        <label for="autoc-id">输入页面标题进行搜索</label>
                        <input id="autoc-id" class="wp100"/>
                    </div>
                    <div class="frame" id="_page_custom">
                        <label for="n_item_name">菜单项名</label>
                        <div data-role="input-control" class="input-control text">
                            <input type="text" id="n_item_name" placeholder="名称"/>
                        </div>
                        <label for="n_title">提示</label>
                        <div data-role="input-control" class="input-control text">
                            <input type="text" id="n_title" placeholder="提示"/>
                        </div>
                        <label for="n_url">URL</label>
                        <div data-role="input-control" class="input-control text">
                            <input type="text" id="n_url" placeholder="URL"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="span4">打开窗口</div>
                <div class="span8 input-control select" data-role="input-control">
                    <select id="target">
                        <option value="_self">原窗口</option>
                        <option value="_blank">新窗口</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="span4"></div>
                <div class="span8">
                    <a class="button primary" id="add2menu" href="#">添加到菜单</a>
                </div>
            </div>
        </div>
        <div id="page-body">
            <div class="post-body-form">
            {if count($items)}
             <ol id="menuitem-list" class="sortable">
             	{'output_menu_items'|fire:$items}
             </ol>
            {else}
            	<ol id="menuitem-list" class="sortable"></ol>
                <div class="post-body-plain" id="menu-instructions"><p>从右侧挑选一些项目（页面、分类目录、链接等）来开始构建您的自定义菜单。</p></div>
            {/if}
            <div class="row" style="margin-top:30px;">				
				<div class="span5">
				    <a class="button large default" tabindex="8" href="{$admincp}/admin/menu/"><i class="icon-undo on-left"></i>返回</a>
					<button class="button large success" tabindex="7"><i class="icon-floppy on-left"></i>保存</button>
				</div>
			</div>
            </div>
        </div>
	</div>

	</fieldset>    
</form>

{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['admin/js/menu','jquery/form','jquery/blockit','jquery/validate','jquery/sortable','jquery/select2/select2'], function(app) {
            $(function(){
                var validateRule = {$validateRule};
            	app.initEditForm(validateRule);
            });
        });
</script>
{/block}
