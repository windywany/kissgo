{extends file=$ksg_admincp_layout}
{block name="title"}{'Tags'|ts}{/block}
{block name="admincp_css_block"}
    <style type="text/css">
        #tag-wrap .label{
            padding:3px;				
            margin:5px;
            line-height: 150%;
            display: inline-block;
            cursor: default;
            font-size:14px;
        }
        #tag-wrap .controls{
            margin-left:1px;
        }
        .tags i{
	        cursor:pointer;
        }
        #tag-wrap .label.selected{        	
            padding:5px 3px;
            color:blue;
        }
	</style>
{/block}

{block name="breadcrumb"}
	<li>{'Tags'|ts}</li>
{/block}

{block name="admincp_body"}
<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;</li>
    {foreach $tags_types as $item}
    <li class="{if $item@key == $type}active{/if}">
        <a href="{$_CUR_URL}?type={$item@key}"><i class="icon-tags"></i> {$item}</a>
    </li>
    {/foreach}
</ul>
<div id="tabs">
    <div class="tab-pane active row-fluid" id="tab-tag">
        <div>
            <form class="form-inline" id="tag-form" method="get" action="{$_CUR_URL}">                
                <input type="hidden" name="type" id="tag-type" value="{$type}"/>
				<input type="text" class="span2"
					name="key" value="{$key}" placeholder="关键字" />
				<button type="submit" class="btn">搜索</button>
			</form>
			<div id="tag-wrap">
            {foreach from=$tags item=item}
            <span id="tag_{$item.tag_id}" class="tags label {$labels|random}"><i class="icon-trash icon-white"></i>{$item.tag}</span>
            {/foreach}
            </div>
            <div class="clear" style="height:10px;"></div>
            <div class="btn-group pull-left" style="margin-right:15px;">
                <button class="btn" id="btn-selectall"><i class="icon-check"></i>全选/反选</button>
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                <ul class="dropdown-menu">			            
                   <li><a href="#" id="btn-delete"><i class="icon-delete"></i> 删除</a></li>
                </ul>
            </div>
            <div class="controls pull-left">
                <div class="input-prepend input-append">
                    <span class="add-on">{$type_text}</span><input type="text" size="16" id="new-tag" class="input-medium"><button id="btn-add-tag" type="button" class="btn btn-add"><i class="icon-plus"></i>添加</button>
                </div>
            </div>
            <div class="pagination pull-right">
                {$totalTags|paging:$limit}
			</div>
        </div>
    </div>
</div>
{/block}

{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'tags.js'|here}"></script>
{/block}