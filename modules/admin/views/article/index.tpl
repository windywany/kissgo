{extends file=$ksg_admincp_layout}
{block name="title"}{'Articles'|ts}{/block}

{block name="toolbar"}
    <a class="btn btn-mini btn-success" href="{$ROUTER_URL}/new"><i class="icon-plus-sign"></i> {'New'|ts}</a>
{/block}

{block name="breadcrumb" nocache}
<li>{'Articles'|ts}</li>
{/block}

{block name="admincp_body"}

<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;</li>
    <li {if $status=='draft'}class="active"{/if}><a href="{$ROUTER_URL}"><i class="icon-file"></i> 草稿箱</a></li>    
    <li {if $status=='published'}class="active"{/if}><a href="{$ROUTER_URL}/published" class="tgre"><i class="icon-check"></i> 已发布</a></li>        
    <li {if $status=='trash'}class="active"{/if}><a href="{$ROUTER_URL}/new" class="tgre"><i class="icon-plus-sign"></i> {'New'|ts}</a></li>
</ul>
<div>  						
    <form class="well form-inline" method="get" action="{$ROUTER_URL}/{$status}">
        <input type="text" class="input-xlarge" name="title" value="{$title}" placeholder="标题"/>
        
        <div class="input-append date datepicker" id="time1">
	    	<input type="text" class="w90" name="time1" value="{$time1}" placeholder="从" readonly/>
	    	<span class="add-on"><i class="icon-calendar"></i></span>
	    </div>
	    <div class="input-append date datepicker" id="time2">
	    	<input type="text" class="w90" name="time2" value="{$time2}" placeholder="到" readonly/>
	    	<span class="add-on"><i class="icon-calendar"></i></span>
	    </div>
        
        <button type="submit" class="btn">搜索</button>
        <a href="{$ROUTER_URL}/{$status}" class="btn">重置</a>
        <a href="#" class="btn" id="use-advanced-search">高级</a>
        <input type="hidden" value="{$ad}" name="ad" id="use-advanced"/>
        <div id="advanced-search-wrapper" class="hide mgt5">        	
        	<label class="checkbox">
				<input type="checkbox" name="mc" {$mc|checked:1}/>由我创建
			</label>			
        </div>
	</form>
        
							    
    <table id="article-list" class="table table-striped table-bordered table-condensed ui-table">
    	<thead>
    		<tr>
    			<th class="col_chk"><input type="checkbox"/></th>
    			<th class="w60 txt-ac">{'#'|sorth:aid}</th>
    			<th class="wa">{'标题'|sorth:'ART.create_time'}</th>								
    			<th class="w350 txt-ac">{'页面'|sorth:'ART.nid'}</th>
    			<th class="w120 txt-ac">{'更新'|sorth:'ART.update_time'}</th>														
    		</tr>
    	</thead>
    	<tbody>
    		{foreach from=$items item=item}
    		<tr>
    			<td class="col_chk"><input type="checkbox" value="{$item.aid}"/></td>
    			<td class="txt-ac">{$item.aid}</td>
    			<td class="has-row-actions">
    				<p>
    					<span class="label label-info mg-r5">由 {$item.user_name} 创建于										
    					{$item.create_time|date_format:'%Y-%m-%d %H:%M'}
    					</span>																	
    				</p>
    				<p>	
    					<a href="{$editURL}/{$item.aid}" title="点击编辑">{$item.title}</a>    					
    				</p>
    				<div class="row-actions">{'get_article_operation'|fire:$item}</div>
    			</td>								
    			<td>
    				{if $item.nid gt 0}
    					<a href="#" class="ksg-publish" data-type="plain" data-content="{$item.aid}">[{$item.nid}] {$item.page_title}</a><br/>
    					<span class="label label-info mgt5">{$item.menu_name}</span><br/>
    					<span class="label mgt5">{$item.status|status:$statusText}</span>
    				{else}
    					<a href="#" class="ksg-publish" data-type="plain" data-content="{$item.aid}" data-title="{$item.title}">发布为页面</a>
    				{/if}
    			</td>
    			<td class="txt-ac">
    				<strong>{$item.update_user_name}</strong><br/>
    				{$item.update_time|date_format:'%Y-%m-%d %H:%M'}
    			</td>													
    		</tr>
    		{foreachelse}
    		<tr>
    			<td colspan="5" class="txt-ac">No Article</td>
    		</tr>
    		{/foreach}
    	</tbody>
    </table>    
    		    
    <div class="form-horizontal">
    	<div class="control-group pull-left">
    		<div class="btn-group">
    			<button class="btn" id="btn-selectall"><i class="icon-check"></i>全选/反选</button>
    			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
    			<ul id="page-dropdown-menu" class="dropdown-menu">
    			{'get_article_bench_options'|fire}
    			</ul>
    		</div>
    	</div>
    	<div class="pagination pull-right">
    		{$countTotal|paging:$limit}
    	</div>
    </div>	    
	
</div>

{/block}
{block name="admincp_foot_js_block"}
	<script type="text/javascript" src="{'list.js'|here}"></script>
{/block}