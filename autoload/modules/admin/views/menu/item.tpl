{foreach $items as $item}
{if $adding}
<li id="menu-item-{$item.id}">
{/if}
<div class="menu-wrap menu-wrap-inactive">
	<dl class="menu-item-bar">
		<dt class="menu-item-handle">
			<span class="item-title">{$item.name}</span>
			<span class="item-controls">
				<a href="#" class="item-edit" title="编辑"><i class="icon-pencil"></i></a>
				<a href="#" class="item-del" title="删除" data-value="{$item.id}"><i class="icon-remove"></i></a>
			</span>
		</dt>
	</dl>
    <input type="hidden" class="parent" name="item[{$item.id}][parent]" value="{$item.parent}"/>
    <input type="hidden" class="sort" name="item[{$item.id}][sort]" value="{$item.sort}"/>
    <input type="hidden" name="item[{$item.id}][name]" class="item_name" value="{$item.name}"/>
    <input type="hidden" name="item[{$item.id}][title]" class="title" value="{$item.title}"/>
    {if $item.type == 'url'}
    <input type="hidden" name="item[{$item.id}][url]" class="url" value="{$item.url}"/>
    {/if}
    <input type="hidden" name="item[{$item.id}][target]" class="target" value="{$item.target}"/>
</div>
{if $adding}
</li>
{/if}
{/foreach}