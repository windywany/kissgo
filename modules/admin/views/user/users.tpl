{extends file=$ksg_admincp_layout}
{block name="title"}{'Users & Roles Managerment'|ts}{/block}
{block name="breadcrumb" nocache}
<li>{'Users'|ts}</li>
{/block}
{block name="admincp_body"}
<div class="tabbable">
        <ul class="nav nav-tabs">
		    <li>&nbsp;&nbsp;</li>	
			<li class="active"><a href="{$_CUR_URL}"><i class="icon-user"></i>用户列表</a></li>
			<li><a href="{$_CUR_URL}/add"><i class="icon-plus"></i>新增用户</a></li>			    
        </ul>
        <div class="tab-content">
            <div class="tab-pane active">
                <form class="form-inline" method="get" action="{$_CUR_URL}">				
				<input type="text" class="input-medium"
					name="login" value="{$login}" placeholder="账户名" /> 				
				<input type="text" class="input-medium"
					name="email" value="{$email}" placeholder="邮箱" /> 
				{html_options name=rid options=$role_options selected=$rid} 
				<label class="radio">
				    <input type="radio" name="status" value=""{$status|checked:''}/>全部
				</label> 
				<label class="radio tgre">
				    <input type="radio" name="status" value="1"{'1'|checked:$status}/>活动
				</label> 
				<label class="radio tred"> 
				    <input type="radio" name="status" value="0"{'0'|checked:$status}/>禁用
				</label>
				<button type="submit" class="btn">搜索</button>
			</form>
            <table id="user-list" class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th class="col_chk"><input type="checkbox"/></th>						
						<th class="w120">{'账户名'|sorth:login}</th>						
						<th class="w150">{'邮箱'|sorth:email}</th>
						<th class="txt-ac w50">{'状态'|sorth:status}</th>
						<th class="wa">角色</th>
						<th class="w150 txt-ac">操作</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$users item=user}
					<tr>
						<td class="col_chk"><input type="checkbox" value="{$user.uid}"/></td>						
						<td>{$user.login}</td>						
						<td>{$user.email}</td>
						<td class="txt-ac">{$user.status|status:$stas}</td>
						<td>
							{'user_belongs'|fire:$user}
							<a href="#{$user.uid}" class="add-to-group pull-right"><i class="icon-plus-sign"></i></a>
						</td>
						<td class="tools">{'get_user_options'|fire:$user}</td>
					</tr>
					{foreachelse}
					<tr>
						<td colspan="8" class="txt-ac">无记录</td>						
					</tr>
					{/foreach}
				</tbody>
			</table>
			<div class="form-horizontal">				
					<div class="btn-group">
						<button class="btn" id="btn-selectall" href="#"><i class="icon-check"></i>全选/反选</button>
			          	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			          	<ul class="dropdown-menu">			            
			            	{'get_user_bench_options'|fire}			            		            
			          	</ul>
			    	</div>				
				<div class="pagination pull-right">
					{$totalUser|paging:$limit}
			    </div>
		    </div>
            </div>
        </div>
    </div>
    <div class="modal hide" id="group-form">
		  	<div class="modal-header">
			    <button class="close" data-dismiss="modal">×</button>
			    <h3>选择用户组</h3>
		  	</div>
			  <div class="modal-body" style="max-height:300px;overflow:auto;">
			  	<ul>
			  		{foreach from=$roles item=g}
			    	<li><label class="checkbox"><input type="checkbox" value="{$g.id}" rel="{$g.label}"/>{$g.name}({$g.label})</label></li>
			    	{/foreach}
			    </ul>
			  </div>
			  <div class="modal-footer">
			    <a href="#" class="btn" id="btn-close-form">关闭</a>
			    <a href="#" class="btn btn-primary" id="btn-done">确定</a>
			  </div>
		</div>
		<script type="text/javascript" src="{'users.js'|here}"></script>				
{/block}
