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
			</span>
		</dt>
	</dl>
	<div class="form-inline">
		<div>
	    	<input type="hidden" class="up_id" name="item[{$item.menuitem_id}][up_id]" value="{$item.up_id}"/>
	        <input type="hidden" class="sort" name="item[{$item.menuitem_id}][sort]" value="{$item.sort}"/>
	       	<div class="input-prepend">
	        	<span class="add-on">名称</span><input type="text" name="item[{$item.menuitem_id}][item_name]" class="span3" value="{$item.item_name}"/>
	     	</div>
	     	<br class="clear"/>
	        <div class="input-prepend mgt5">
	           	<span class="add-on">属性</span><input type="text" name="item[{$item.menuitem_id}][title]" class="span3" value="{$item.title}"/>
	        </div>
	        {if $item.type=='url'}
	        <br class="clear"/>
	        <div class="input-prepend mgt5">
	           	<span class="add-on">URL</span><input type="text" name="item[{$item.menuitem_id}][url]" class="span3" value="{$item.url}"/>
	        </div>
	     	{/if}
	     	<br class="clear"/>
	       	<label class="radio"><input type="radio" name="item[{$item.menuitem_id}][target]" {'_blank'|checked:$item.target} value="_blank"/>新窗口</label>
	        <label class="radio"><input type="radio" name="item[{$item.menuitem_id}][target]" {'_self'|checked:$item.target} value="_self"/>原窗口</label>
	    	<br class="clear"/>
	        <div class="tools mgt5">	      		         	
	        	<a href="./?Ctlr=DelMenuItem&miid={$item.menuitem_id}" class="del-item" title="删除"><i class="icon-trash"></i>移除</a>
	    	</div>
    	</div>
	</div>
</div>
{if $adding}
</li>
{/if}
{/foreach}