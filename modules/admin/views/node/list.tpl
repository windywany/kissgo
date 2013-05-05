{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages'|ts}{/block}
{block name="admincp_css_block"}
<link rel="stylesheet" href="{'jquery/plugins/ztree/ztree.css'|static}" />
{/block}
{block name="breadcrumb" nocache}
<li>{'Pages'|ts}</li>
{/block}
{block name="admincp_body"}

<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;</li>
    <li><a href="{$_CUR_URL}"><i class="icon-check"></i> 已发布</a></li>
    <li class="active"><a href="{$_CUR_URL}/draft"><i class="icon-file"></i> 草稿箱</a></li>    
    <li><a href="{$_CUR_URL}/trash"><i class="icon-trash"></i> 回收站</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane active">
	    
	    
	    <div>						
            <form class="well form-inline" method="get" action=".">
							<input type="hidden" name="status" value="{$status}"/>							
							<input type="text" class="input-medium" name="title" value="{$title}" placeholder="标题"/>							
							<select name="page_type" class="input-small">
								{html_options options=$page_types selected=$page_type}
							</select>
							<select name="tag" class="input-small">
								{html_options options=$tag_options selected=$tag}
							</select>
														
							<button type="submit" class="btn">搜索</button>
							<!--button type="button" class="btn">高级..</button-->
						</form>
					</div>
							    
					<table id="page-list" class="table table-striped table-bordered table-condensed">
						<thead>
							<tr>
								<th class="col_chk"><input type="checkbox"/></th>
								<th class="w50 txt-ac">{'#'|sorth:page_id}</th>
								<th class="wa">{'详细'|sorth:create_time}</th>								
								<th class="w80 txt-ac">{'类型'|sorth:page_type}</th>															
								<th class="w200 txt-ac">标签</th>
								<th class="w120 txt-ac">{'更新'|sorth:upate_time}</th>														
							</tr>
						</thead>
						<tbody>
							{foreach from=$items item=item}
							<tr>
								<td class="col_chk"><input type="checkbox" value="{$item.page_id}"/></td>
								<td class="txt-ac">{$item.page_id}</td>
								<td class="has-row-actions">
									<p>
										<span class="label mg-r5">由 {$item.user_name} 创建于										
										{$item.create_time|date_format:'%Y-%m-%d %H:%M'}
										</span>
										{if $item.publish_time}
											<span class="label label-success mg-r5">发布于
												{$item.update_time|date_format:'%Y-%m-%d %H:%M'}
											</span>
										{/if}
										{if $item.comments gt 0}
										<a href="../comment/?page_id={$item.page_id}"><span class="badge badge-info">{$item.comments}</span></a>
										{/if}																
									</p>
									<p>
										<a href="{$item|url}?preview" target="_blank" title="点击预览">{$item.title}</a>
										{'show_page_flags'|fire:$item}
									</p>
									<div class="row-actions">{'get_page_operation'|fire:$item}</div>
								</td>								
								<td class="txt-ac">{$item.page_type|status:$page_types}</td>						
								<td>
									{if $item.category}
									<p>
										<span class="label mg-r5">栏目</span>
										<a href="./?status={$status}&category={$item.category}&cname={$item.category_name}">{$item.category_name}</a>
									</p>
									{/if}
									{if $item.subcategory}
									<p>
										<span class="label mg-r5">副栏目</span>
										<a href="./?status={$status}&category={$item.subcategory}&cname={$item.sub_category_name}">{$item.sub_category_name}</a>
									</p>
									{/if}
								</td>
								<td class="txt-ac">
									<strong>{$item.update_user_name}</strong><br/><br/>
									{$item.update_time|date_format:'%Y-%m-%d %H:%M'}
								</td>													
							</tr>
							{foreachelse}
							<tr>
								<td colspan="6" class="txt-ac">无记录</td>
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
								{'get_page_bench_options'|fire:$status}
								</ul>
							</div>
						</div>
						<div class="pagination pull-right">
							{$countTotal|paging:$limit}
						</div>
					</div>	
	    
	    
	    
	</div>
</div>
<div class="modal hide fade" tabindex="-1" id="tpl-selector-box" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button class="close" data-dismiss="modal">×</button>
        <h3>选择模板</h3>
    </div>
    <div class="modal-body" style="max-height:300px;overflow:auto;">
        <ul class="ztree" id="tpls-tree"></ul>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="btn-close-form" data-dismiss="modal">关闭</a>
        <a href="#" class="btn btn-primary" id="btn-done">确定</a>
    </div>
</div>
		
{/block}
{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'jquery/plugins/ztree/ztree.js'|static}"></script>
<script type="text/javascript" src="{'list.js'|here}"></script>
{/block}