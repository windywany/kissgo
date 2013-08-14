{extends file=$ksg_admincp_layout}

{block name="title"}{'Medias'|ts}{/block}

{block name="toolbar"}
    <a class="btn btn-mini btn-success" href="{$_CUR_URL}/upload"><i class="icon-upload"></i> {'Upload'|ts}</a>
{/block}

{block name="breadcrumb"}
	<li>{'Medias'|ts}</li>	
{/block}

{block name="admincp_body"}
            <ul class="nav nav-tabs">
				<li>&nbsp;&nbsp;</li>	
			    <li class="active"><a href="#"><i class="icon-picture"></i>文件列表</a></li>
			    <li><a href="{$_CUR_URL}/upload"><i class="icon-upload"></i>上传</a></li>			    
		    </ul>		    
		    <div class="txt-ar">
				<form class="form-inline pull-left" method="get" action="{$_CUR_URL}">
					<div class="input-prepend">
						<span class="add-on">文件</span>
				    	<input type="text" class="input-medium" name="name" value="{$name}" placeholder="文件"/>
				    </div>
				    <div class="input-append date datepicker" id="time1">
				    	<input type="text" class="w90" name="time1" value="{$time1}" placeholder="日期" readonly/>
				    	<span class="add-on"><i class="icon-calendar"></i></span>
				    </div>
				    <div class="input-append date datepicker" id="time2">
				    	<input type="text" class="w90" name="time2" value="{$time2}" placeholder="日期" readonly/>
				    	<span class="add-on"><i class="icon-calendar"></i></span>
				    </div>
				    <div class="input-prepend">
						<span class="add-on">类型</span>
				    	<select name="type" class="w100">{html_options options=$type_options selected=$type}</select>
				    </div>
				    <button type="submit" class="btn btn-primary"><i class="icon-filter"></i> {'Search'|ts}</button>
				    <a href="{$_CUR_URL}" class="btn"><i class="icon-repeat"></i> {'Reset'|ts}</a>
			    </form>
		    </div>		    
		    <table id="media-list" class="table table-striped table-bordered table-condensed ui-table">
				<thead>
					<tr>
						<th class="col_chk"><input type="checkbox"/></th>						
						<th class="wa">{'文件'|sorth:name}</th>
						<th class="w50 txt-ac">{'类型'|sorth:type}</th>						
						<th class="w120">{'上传'|sorth:create_time}</th>						
					</tr>
				</thead>
				<tbody>
					{foreach from=$items item=item}
					<tr>
						<td class="col_chk"><input type="checkbox" class="chkbx" aid="{$item.url}" value="{$item.attachment_id}"/></td>						
						<td class="has-row-actions">
							<div class="thumbnail pull-left mg-r5">								
								<a href="{$item.url|uploaded}" title="{$item.name}" {if $item.type == 'image'}rel="prettyPhoto[pp_gal]"{/if}>								
									<img class="media" src="{'media_icon'|fire:$item}" title="{$item.alt_text}" alt="{$item.alt_text}"/>
								</a>								
							</div>
							<div class="pull-left media-info">
								<strong class="att_name">{$item.name}</strong>
								<p>
									<span class="label label-info">{$item.ext}</span>									
								</p>								
								<div class="form-inline hide">
									<input type="text" class="input-small a_name" value="{$item.name}">
								    <input type="text" class="span2 a_alt" value="{$item.alt_text}">
								    <button class="btn btn-primary btn-edit-att"><i class="icon-ok"></i></button>
								    <button class="btn btn-ca-att"><i class="icon-remove"></i></button>
								</div>
								<div class="row-actions">
									{'get_media_actions'|fire:$item}
								</div>
							</div>						
						</td>
						<td class="txt-ac">{$item.type|status:$type_options}</td>						
						<td>
							{$item.create_time|date_format}<br/>
							{if $item.author}
							<span class="label">{$item.author}</span>
							{/if}
						</td>
					</tr>
					{foreachelse}
					<tr>
						<td colspan="4" class="txt-ac">无记录</td>						
					</tr>
					{/foreach}
				</tbody>
			</table>
			<div class="form-horizontal">
				<div class="control-group pull-left">
					<div class="btn-group">
						<button class="btn" id="btn-selectall"><i class="icon-check"></i>全选/反选</button>
			          	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			          	<ul class="dropdown-menu">			            
			            	{'get_media_bench_options'|fire}			            		            
			          	</ul>
			    	</div>
				</div>
				<div class="pagination pull-right">
					{$total|paging:$limit}
			    </div>
		    </div>
{/block}
{block name="admincp_foot_js_block"}	
	<script type="text/javascript" src="{'list.js'|here}"></script>
{/block}