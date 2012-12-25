{extends file="kissgo/admincp_small_sidebar.tpl"}
{block name="title"}Users & Roles Managerment{/block}
{block name="sidebar"}
    <div class="tabbable tabs-left">
        <ul class="nav nav-tabs" style="min-height:385px;">
		    <li>&nbsp;&nbsp;</li>	
			<li class="active"><a href="#">用户账户列表</a></li>
			<li><a href="./?Ctlr=AddUser">新增用户</a></li>			    
        </ul>
    </div>
{/block}
{block name="breadcrumb"}
<li>{'Users'|ts}</li>
{/block} 
{block name="admincp_body"}	
			<form class="well form-inline" method="get" action="{'kissgo'|murl:users}">
				<input type="text" class="input-small" name="uid" value="{$uid}"
					placeholder="UID" /> 
				<input type="text" class="input-medium"
					name="uname" value="{$uname}" placeholder="用户名" /> 
				<input
					type="text" class="input-small" name="name" value="{$name}"
					placeholder="姓名" /> 
				<input type="text" class="input-medium"
					name="email" value="{$email}" placeholder="邮箱" /> 
					{html_options name=gid options=$group_options selected=$gid} 
				<label class="radio">
				    <input type="radio" name="status" value=""{$status|checked:''}/>全部
				</label> 
				<label class="radio tgre"> 
				    <input type="radio" name="status" value="0"{'0'|checked:$status}/>活动
				</label> 
				<label class="radio tred"> 
				    <input type="radio" name="status" value="1"{'1'|checked:$status}/>禁用
				</label>
				<button type="submit" class="btn">搜索</button>
			</form>


			<table id="user-list" class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th class="col_chk"><input type="checkbox" /></th>
						<th class="w50 txt-ac">UID</th>
						<th class="w150">用户名</th>
						<th class="w120">姓名</th>
						<th class="w200">邮箱</th>
						<th class="txt-ac w50">状态</th>
						<th class="wa">角色</th>
						<th class="w150 txt-ac">操作</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$users item=user}
					<tr>
						<td class="col_chk"><input type="checkbox" value="{$user.uid}" /></td>
						<td class="txt-ac">{$user.uid}</td>
						<td>{$user.uname}</td>
						<td>{$user.name}</td>
						<td>{$user.email}</td>
						<td class="txt-ac">{$user.status|status:$stas}</td>
						<td>{'user_belongs'|fire:$user} 
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
					<button class="btn" id="btn-selectall">
						<i class="icon-check"></i>全选/反选
					</button>
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> 
					    <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						{'get_user_bench_options'|fire}
						<li><a href="#">adfasdfa adsf afasdfasdf</a></li>
						<li><a href="#">adfasdfa adsf afasdfasdf</a></li>
						<li><a href="#">adfasdfa adsf afasdfasdf</a></li>
					</ul>
				</div>
				<div class="pagination pull-right">{$totalUser|paging:$limit}</div>
			</div>		
<div class="modal hide" id="group-form">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3>选择用户组</h3>
	</div>
	<div class="modal-body" style="max-height: 300px; overflow: auto;">
		<ul>
			{foreach from=$groups item=g}
			<li>
			    <label class="checkbox"><input type="checkbox" value="{$g.gid}" rel="{$g.name}" />{$g.name}({$g.gname})</label>
			</li>
		    {/foreach}
		</ul>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" id="btn-close-form">关闭</a> 
		<a href="#" class="btn btn-primary" id="btn-done">确定</a>
	</div>
</div>
{/block}
