{extends file=$ksg_admincp_layout}
{block name="title"}{'Users & Roles Managerment'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Roles'|ts}</li>
{/block}
{block name="admincp_body"}
<div class="tabbable">
        <ul class="nav nav-tabs">
		    <li>&nbsp;&nbsp;</li>	
			<li class="active"><a href="{$_CUR_URL}"><i class="icon-user"></i> 角色列表</a></li>
			<li><a href="{$_CUR_URL}/add"><i class="icon-plus"></i> 新增角色</a></li>			    
        </ul>
        <div class="tab-content">
            <div class="tab-pane active">
                <form class="form-inline" method="get" action="{$_CUR_URL}">				    
				    <input type="text" class="input-medium" name="label" value="{$label}" placeholder="角色标识">
				    <input type="text" class="input-small" name="name" value="{$name}" placeholder="角色名">				    
				    <button type="submit" class="btn">搜索</button>
			    </form>
                <table id="group-list" class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th class="col_chk"><input type="checkbox"/></th>						
						<th class="w100">{'角色标识'|sorth:label}</th>
						<th class="w200">{'角色名'|sorth:name}</th>						
						<th class="w50">内置</th>	
						<th class="wa">备注</th>	
						<th class="w150 txt-ac">操作</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$items item=item}
					<tr>
						<td class="col_chk"><input type="checkbox" value="{$item.rid}"/></td>						
						<td>{$item.label}</td>
						<td>{$item.name}</td>						
						<td class="txt-ac">{$item.reserved|status:$reserves}</td>
						<td>{$item.note|escape}</td>
						<td class="tools">{'get_role_options'|fire:$item}</td>
					</tr>
					{foreachelse}
					<tr>
						<td colspan="6" class="txt-ac">无记录</td>						
					</tr>
					{/foreach}
				</tbody>
			</table>
			<div class="form-horizontal">				
					<div class="btn-group">
						<button class="btn" id="btn-selectall"><i class="icon-check"></i>全选/反选</button>
			          	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			          	<ul class="dropdown-menu">			            
			            	{'get_role_bench_options'|fire}			            		            
			          	</ul>
			    	</div>
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