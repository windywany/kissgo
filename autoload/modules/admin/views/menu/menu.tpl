{extends file=$layout}
{block name="layout_style_block"}
	<link href="{'../css/menu.css'|here}"	rel="stylesheet" />
{/block}
{block name="subtitle"}导航菜单{/block}
{block name="workbench"}

<div class="sidebar light">
    <ul>
        <li><a href="{$admincp}/admin/menu/add/"><i class="icon-plus-2 fg-green"></i>新增菜单</a></li>
        {foreach $menus as $menu}
        <li {if $menu.is_default}class="stick bg-red"{/if}>
            <a href="{$admincp}/admin/menu/edit/{$menu.id}">{$menu.name}({$menu.alias})
            {if !$menu.is_default}
            <i class="icon-star-4 fg-green on-right set-default" data-value="{$menu.id}"></i>
            {/if}
            </a>
        </li>
        {/foreach}
    </ul>
</div>


{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['admin/js/menu','jquery/blockit'], function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}
