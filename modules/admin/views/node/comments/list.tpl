{extends file=$ksg_admincp_layout}
{block name="title"}{'Comments'|ts}{/block}
{block name="breadcrumb" nocache}
<li>{'Comments'|ts}</li>
{/block}
{block name="admincp_body"}

<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;</li>
    <li {if $status=='new'}class="active"{/if}><a href="{$_CUR_URL}"><i class="icon-comment"></i> 最新评论({$newTotal})</a></li>
    <li {if $status=='pass'}class="active"{/if}><a href="{$_CUR_URL}?pass"><i class="icon-thumbs-up"></i> 已批准</a></li>    
    <li {if $status=='unpass'}class="active"{/if}><a href="{$_CUR_URL}?unpass"><i class="icon-thumbs-down"></i> 未批准</a></li>
    <li {if $status=='spam'}class="active"{/if}><a href="{$_CUR_URL}?spam"><i class="icon-fire"></i> 垃圾评论</a></li>
    <li {if $status=='trash'}class="active"{/if}><a href="{$_CUR_URL}?trash"><i class="icon-trash"></i> 回收站</a></li>
</ul>
<div>
	<div>
	    <div>
            <form action="{$_CUR_URL}" method="get" class="form-inline" id="comment-search-form">
                <input type="hidden" name="{$status}" value=""/>
                <input type="text" placeholder="页面ID" value="{$nid}" name="nid" class="input-small"/>					    
                <input type="text" placeholder="搜索词" value="{$key}" name="key" class="input-medium"/>				    	    
                <button class="btn" type="submit"><i class="icon-search"></i> 搜索</button>
            </form>
        </div>
        <table id="comment-list" class="table table-striped table-bordered table-condensed ui-table">
						<thead>
							<tr>
								<th class="col_chk"><input type="checkbox"/></th>								
								<th class="w200">{'作者'|sorth:author}</th>
								<th class="wa">{'评论'|sorth:create_time}</th>
								<th class="w120">{'页面'|sorth:node_id}</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$items item=item}
							<tr>
								<td class="col_chk"><input type="checkbox" value="{$item.id}"/></td>								
								<td class="author-td">
									<img class="avator"/>
									<div class="author">
										{if $item.author}
										<strong>{$item.author}</strong>										
										{/if}
										{if $item.email}
										<br/>
										<a href="mailto:{$item.email}" title="{$item.email}">{$item.email|truncate:30}</a>
										{/if}
										{if $item.url}
										<br/>
										<a href="{$item.url}" target="_blank" title="{$item.url}">{$item.url|truncate:30}</a>
										{/if}
									</div>									
									<span class="label label-info">{$item.source_ip}</span>							
								</td>
								<td class="has-row-actions">
									<div class="sbt-info mgb10">
										<span class="label mg-r5">提交于</span>
										<a href="{$item.page_url}#comment-{$item.id}" target="_blank">
										{$item.create_time|date_format:'%Y-%m-%d %H:%M'}
										</a>
										{if $item.reply_id}
										<span class="label label-warning mg-r5">回复给</span>
										<a href="{$item.page_url}#comment-{$item.reply_id}" target="_blank">
										{$item.reply_author}
										</a>
										{/if}
										<br/>
										<p><span class="label mg-r5">主题</span><span class="label-subject">{$item.subject}</span></p>
									</div>
									<p class="wrapper-comment">{$item.comment|nl2br}</p>
									<div class="row-actions">
									    <input type="hidden" name="author" value="{$item.author|escape}"/>
									    <input type="hidden" name="subject" value="{$item.subject|escape}"/>
									    <input type="hidden" name="comment" value="{$item.comment|escape}" />
									    <input type="hidden" name="url" value="{$item.url}"/>
									    <input type="hidden" name="email" value="{$item.email}" />
									    {'get_comment_operations'|fire:$item}
									</div>
								</td>
								<td><a href="{$item.page_url}" title="{$item.page_title}" target="_blank">{$item.page_title|truncate:24}</a></td>								
							</tr>
							{foreachelse}
							<tr>
								<td colspan="5" class="txt-ac">无评论</td>						
							</tr>
							{/foreach}
						</tbody>
					</table>
					
					<div class="form-horizontal">
						<div class="control-group pull-left">
							<div class="btn-group">
								<button class="btn btn-selectall"><i class="icon-check"></i>全选/反选</button>
					          	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
					          	<ul class="dropdown-menu">
					            	{'get_comment_bench_options'|fire:$status}
					          	</ul>
					    	</div>
						</div>
						<div class="pagination pull-right">
							{$totalCount|paging:$limit}
					    </div>
				    </div>
        
        
	</div>
</div>
	
<div class="modal hide fade" tabindex="-1" id="reply-cmt-box" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <button class="close" data-dismiss="modal">×</button>
        <h3>回复评论</h3>
    </div>
    <div class="modal-body" style="max-height:500px;">
        <div class="row-fluid">
            <div class="span8">
                <label for="subject">Subject</label>
                <input type="text" id="subject" style="width:95%"/>
            </div>
            <div class="span4">
                <label for="author">Author</label>
                <input type="text" id="author" style="width:90%"/>
            </div>
        </div>
        <div class="row-fluid when-edit">
            <label for="url">URL</label>
            <input type="text" id="url" class="span12"/>
            <label for="email">Email</label>
            <input type="text" id="email" class="span12"/>            
        </div>
        <div class="quicktags" id="quicktags">
            <textarea rows="5" class="quicktags-editor" name="comment" id="comment"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="btn-close-form" data-dismiss="modal">关闭</a>
        <a href="#" class="btn btn-primary" id="btn-done">确定</a>
    </div>
</div>

{/block}
{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'quicktags.js'|static}"></script>
<script type="text/javascript" src="{'list.js'|here}"></script>
{/block}