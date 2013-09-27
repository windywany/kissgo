{foreach $items as $item}
{if $adding}
<li id="menu-item-{$item.menuitem_id}">
{/if}
<div class="menu-wrap menu-wrap-inactive">
	<dl class="menu-item-bar">
		<dt class="menu-item-handle">
			<span class="item-title">{$item.item_name}</span>
			<span class="item-controls">
				<span class="item-type">{$item.type_name}</span>
				<a href="#" class="item-edit edit-item" title="编辑"><i class="icon-edit"></i></a>
				<a href="{$_CUR_URL}/del?miid={$item.menuitem_id}" class="item-del del-item" title="删除"><i class="icon-trash"></i></a>
			</span>
		</dt>
	</dl>
        <input type="hidden" class="up_id" name="item[{$item.menuitem_id}][up_id]" value="{$item.up_id}"/>
        <input type="hidden" class="sort" name="item[{$item.menuitem_id}][sort]" value="{$item.sort}"/>
        <input type="hidden" name="item[{$item.menuitem_id}][item_name]" class="item_name span3" value="{$item.item_name}"/>	       	
        <input type="hidden" name="item[{$item.menuitem_id}][title]" class="title span3" value="{$item.title}"/>
        {if $item.type == 'url'}
        <input type="hidden" name="item[{$item.menuitem_id}][url]" class="url span3" value="{$item.url}"/>
        {else}
        <input type="hidden" class="pagename span3" value="{$item.pagename}"/>        
        {/if}
        <input type="hidden" name="item[{$item.menuitem_id}][target]" class="target span3" value="{$item.target}"/>	
</div>
{if $adding}
</li>
{/if}
{/foreach}