{extends file=$ksg_admincp_layout}
{block name="title"}{'Pages'|ts}{/block}
{block name="admincp_css_block"}
<link rel="stylesheet" href="{'bootstrap/select2/select2.css'|static}"/>
{/block}
{block name="breadcrumb" nocache}
<li>{'Pages'|ts}</li>
{/block}
{block name="admincp_body"}

<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;</li>
    <li {if $status=='draft'}class="active"{/if}><a href="{$_CUR_URL}"><i class="icon-file"></i> 草稿箱({$draftTotal})</a></li>
    <li {if $status=='approving'}class="active"{/if}><a href="{$_CUR_URL}?approving" class="torg"><i class="icon-star-empty"></i> 待审核({$approvingTotal})</a></li>
    <li {if $status=='approved'}class="active"{/if}><a href="{$_CUR_URL}?approved" class="tgre"><i class="icon-thumbs-up"></i> 已审核</a></li>
    <li {if $status=='published'}class="active"{/if}><a href="{$_CUR_URL}?published" class="tgre"><i class="icon-check"></i> 已发布</a></li>
        <li {if $status=='unapproved'}class="active"{/if}><a href="{$_CUR_URL}?unapproved" class="torg"><i class="icon-thumbs-down"></i> 未批准</a></li>
    <li {if $status=='trash'}class="active"{/if}><a href="{$_CUR_URL}?trash" class="tred"><i class="icon-trash"></i> 回收站</a></li>
</ul>
<div class="tab-content">
	<div>
	    <div>						
            <form class="well form-inline" method="get" action="{$_CUR_URL}">
                <input type="hidden" name="{$status}" value=""/>							
                <input type="text" class="input-medium" name="title" value="{$title}" placeholder="标题"/>							
                <select name="node_type" class="input-medium">
                    {html_options options=$page_types selected=$page_type}
                </select>
                <select name="flag" class="input-medium">
                    {html_options options=$flags selected=$flag}
                </select>
                <input type="hidden" name="tag" id="ipt-tag" style="width:350px" data-placeholder="标签"/>
                
                <button type="submit" class="btn">搜索</button>
                <a href="{$_CUR_URL}?{$status}" class="btn">重置</a>
                <!--button type="button" class="btn">高级..</button-->
            </form>
        </div>
							    
					<table id="page-list" class="table table-striped table-bordered table-condensed ui-table">
						<thead>
							<tr>
								<th class="col_chk"><input type="checkbox"/></th>
								<th class="w50 txt-ac">{'#'|sorth:nid}</th>
								<th class="wa">{'详细'|sorth:create_time}</th>								
								<th class="w80 txt-ac">{'类型'|sorth:page_type}</th>															
								<th class="w120 txt-ac">标签</th>
								<th class="w120 txt-ac">{'更新'|sorth:upate_time}</th>														
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
										{if $item.comments gt 0}
										<a href="../comment/?nid={$item.nid}"><span class="badge badge-info">{$item.comments}</span></a>
										{/if}																
									</p>
									<p>
										<a href="{$item|url}?preview" target="_blank" title="点击预览">{$item.title}</a>
										{'show_node_flags'|fire:$item}
									</p>
									<div class="row-actions">{'get_page_operation'|fire:$item}</div>
								</td>								
								<td class="txt-ac">{$item.node_type_name}</td>						
								<td>
								    <div class="row-fluid">
									    {'show_node_tags'|fire:$item}
									</div>
								</td>
								<td class="txt-ac">
									<strong>{$item.update_user_name}</strong><br/>
									{$item.update_time|date_format:'%Y-%m-%d %H:%M'}
								</td>													
							</tr>
							{foreachelse}
							<tr>
								<td colspan="6" class="txt-ac">无页面</td>
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
<script type="text/javascript" src="{'bootstrap/select2/select2.min.js'|static}"></script>
<script type="text/javascript" src="{'list.js'|here}"></script>
{/block}