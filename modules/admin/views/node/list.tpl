{extends file=$ksg_admincp_layout}
{block name="title"}{'Web Pages'|ts}{/block}

{block name="breadcrumb" nocache}	  
    {foreach $fullpaths as $path}
    <li>
    	{if $path.id}
    	<a href="{$_CUR_URL}/{$status}?vpid={$path.id}">{$path.name}</a><span class="divider">/</span>
    	{else}
    	{$path.name}
    	{/if}
    </li>
    {/foreach}    
{/block}
{block name="admincp_body"}

<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;</li>
    <li {if $status=='draft'}class="active"{/if}><a href="{$_CUR_URL}"><i class="icon-file"></i> 草稿箱</a></li>
    <li {if $status=='approving'}class="active"{/if}><a href="{$_CUR_URL}/approving" class="torg"><i class="icon-star-empty"></i> 待审核</a></li>
    <li {if $status=='approved'}class="active"{/if}><a href="{$_CUR_URL}/approved" class="tgre"><i class="icon-thumbs-up"></i> 已审核</a></li>
    <li {if $status=='published'}class="active"{/if}><a href="{$_CUR_URL}/published" class="tgre"><i class="icon-check"></i> 已发布</a></li>
    <li {if $status=='unapproved'}class="active"{/if}><a href="{$_CUR_URL}/unapproved" class="torg"><i class="icon-thumbs-down"></i> 未批准</a></li>
    <li {if $status=='trash'}class="active"{/if}><a href="{$_CUR_URL}/trash" class="tred"><i class="icon-trash"></i> 回收站</a></li>
</ul>
<div>  						
    <form class="well form-inline" method="get" action="{$_CUR_URL}/{$status}">
    	<input type="hidden" value="{$vpid}" name="vpid"/>    	    	
        <input type="text" class="input-xlarge" name="title" value="{$title}" placeholder="标题"/>							
        <select name="node_type" class="input-medium">
            {html_options options=$page_types selected=$node_type}
        </select>
        <select name="flag" class="input-medium">
            {html_options options=$flags selected=$flag}
        </select>        
        <button type="submit" class="btn">搜索</button>
        <a href="{$_CUR_URL}/{$status}" class="btn">重置</a>
        <a href="#" class="btn" id="use-advanced-search">高级</a>
        <input type="hidden" value="{$ad}" name="ad" id="use-advanced"/>
        <div id="advanced-search-wrapper" class="hide mgt5">        	
        	<label class="checkbox">
				<input type="checkbox" name="mc" {$mc|checked:1}/>由我创建
			</label>
			<label class="checkbox">
				<input type="checkbox" name="mp" {$mp|checked:1}/>由我发布
			</label>
			<label class="checkbox">
				<input type="checkbox" name="pwd" {$pwd|checked:1}/>全局搜索
			</label>
        </div>
	</form> 
	<div class="row-fluid">
		<div class="span2 sidebar">
			<ul class="nav nav-list sidenav">
			  <li><a href="{$_CUR_URL}/{$status}?vpid={$prepid}"><i class="icon-chevron-left"></i> {'Go Back'|ts}</a></li>
	          {foreach $paths as $path}
	          <li><a href="{$_CUR_URL}/{$status}?vpid={$path.id}"><i class="icon-chevron-right"></i> {$path.name}</a></li>
	          {/foreach}
	          <li><a class="ksg-publish" data-type="catalog" href="#"><i class="icon-plus"></i>{'New Virtual Directory'|ts}</a></li>
	        </ul>
		</div>
		<div class="span10">	    
    <table id="page-list" class="table table-striped table-bordered table-condensed ui-table">
    	<thead>
    		<tr>
    			<th class="col_chk"><input type="checkbox"/></th>
    			<th class="w60 txt-ac">{'#'|sorth:nid}</th>
    			<th class="wa">{'详细'|sorth:create_time}</th>								
    			<th class="w80 txt-ac">{'类型'|sorth:node_type}</th>
    			<th class="w120 txt-ac">{'更新'|sorth:update_time}</th>														
    		</tr>
    	</thead>
    	<tbody>
    		{foreach from=$items item=item}
    		<tr>
    			<td class="col_chk"><input type="checkbox" value="{$item.nid}"/></td>
    			<td class="txt-ac">{$item.nid}</td>
    			<td class="has-row-actions">
    				<p>
    					<span class="label label-info mg-r5">由 {$item.user_name} 创建于										
    					{$item.create_time|date_format:'%Y-%m-%d %H:%M'}
    					</span>
    					{if $item.publish_time}
    						<span class="label label-success mg-r5">发布于
    							{$item.update_time|date_format:'%Y-%m-%d %H:%M'}
    						</span>
    					{/if}    					    																				
    				</p>
    				<p>	
    					<a href="{$item|url}?preview" target="_blank" title="点击预览">{$item.title}</a>
    					{'show_node_flags'|fire:$item}
    					{'show_node_tags'|fire:$item}
    				</p>
    				<div class="row-actions">{'get_page_operation'|fire:$item}</div>
    			</td>								
    			<td class="txt-ac">
        			{$item.node_type_name}        			
    			</td>
    			<td class="txt-ac">
    				<strong>{$item.update_user_name}</strong><br/>
    				{$item.update_time|date_format:'%Y-%m-%d %H:%M'}
    			</td>													
    		</tr>
    		{foreachelse}
    		<tr>
    			<td colspan="5" class="txt-ac">无页面</td>
    		</tr>
    		{/foreach}
    	</tbody>
    </table>    
    		    
    <div class="form-horizontal">
    	<!-- div class="control-group pull-left">
    		<div class="btn-group">
    			<button class="btn" id="btn-selectall"><i class="icon-check"></i>全选/反选</button>
    			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
    			<ul id="page-dropdown-menu" class="dropdown-menu">
    			{'get_page_bench_options'|fire:$status}
    			</ul>
    		</div>
    	</div -->
    	<div class="pagination pull-right">
    		{$countTotal|paging:$limit}
    	</div>
    </div>	    
	</div>
	</div>					
</div>		
{/block}
{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'list.js'|here}"></script>
{/block}