{extends file=$ksg_admincp_layout}
{block name="title"}{'Page Types'|ts}{/block}
{block name="breadcrumb" nocache}
<li>{'Page Types'|ts}</li>
{/block}
{block name="admincp_body"}

<div style="margin-top:10px;">
<form class="form-inline" id="type-search-form" method="get" action="{$_CUR_URL}">				
				<input type="text" class="input-medium"
					name="type" value="{$type}" placeholder="类型" /> 				
				<input type="text" class="input-medium"
					name="name" value="{$name}" placeholder="类型名" /> 
				<button type="submit" class="btn">搜索</button>
</form>
    <table id="type-list" class="table table-striped table-bordered table-condensed ui-table">
				<thead>
					<tr>
						<th class="col_chk"><input type="checkbox"/></th>
						<th class="w100">{'页面类型'|sorth:type}</th>
						<th class="w150">{'页面类型名称'|sorth:name}</th>
						<th class="w400">默认模板文件名</th>						
						<th class="wa">说明</th>						
					</tr>
				</thead>
				<tbody id="types">
					{foreach from=$items item=item}
					<tr>
						<td class="col_chk"><input type="checkbox" value="{$item.id}"/></td>
						<td><span class="label {if $item.creatable}label-success{/if}">{$item.type}</span></td>
						<td>{$item.name}</td>	
						<td class="has-row-actions">
						    <span class="label label-info type-tpl">{$item.template}</span>
						    <div class="form-inline hide" id="form-inline-{$item.id}">
							    <input type="text" class="input-xlarge tpl" value="{$item.template}"/>								    
							    <button class="btn btn-primary btn-edit-att"><i class="icon-ok"></i></button>
							    <button class="btn btn-ca-att"><i class="icon-remove"></i></button>
							</div>
							<div class="row-actions">
							    <a href="#{$item.id}" class="edit-type"><i class="icon-edit"></i>编辑</a>
							</div>
						</td>
						<td>{$item.note|escape}</td>
					</tr>
					{foreachelse}
					<tr>
						<td colspan="5" class="txt-ac">无记录</td>
					</tr>
					{/foreach}
				</tbody>
    </table>
    <div class="form-horizontal">
        <div class="pagination pull-right">
            {$totalTypes|paging:$limit}
        </div>
    </div>
</div>
{/block}
{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'type.js'|here}"></script>
{/block}
