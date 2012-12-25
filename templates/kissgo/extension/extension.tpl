{extends file="kissgo/admincp_no_sidebar.tpl"}
{block name="title"}Extensions Managerment{/block}
{block name="breadcrumb"}
	<li>{'Extensions'|ts}</li>
{/block}
{block name="admincp_body"}
<div class="tabbable tabs-left">
    <ul class="nav nav-tabs" style="min-height:385px;margin-top:50px;">
        <li>&nbsp;</li>						
        <li class="{if 'installed' == $group}active{/if}">
            <a class="tgre" href="{$_page_url}"><i class="icon-check"></i>已安装({$installedTotal})</a>
        </li>						
        <li class="{if 'uninstall' == $group}active{/if}">
            <a href="{$_page_url}?group=uninstall"><i class="icon-inbox"></i>未安装({$uninstalledTotal})</a>
        </li>					
    </ul>
    <div class="tab-content" style="padding-top:10px;">	                	
        <div class="tab-pane active">	                		
            <div style="margin-bottom:20px;">	                		    
                <span class="mg-r5 tgre">已启用({$etotal})</span> | <span class="mg-l5 torg">可升级({$upgradable})</span> | <span class="mg-l5">未启用({$dtotal})</span>	                			
                {if $uploadable}
	            <button class="btn pull-right mg-r5" id="btn-upload"><i class="icon-upload"></i>上传</button>
	            {/if}
            </div>
            <table id="page-list" class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>                        										
                        <th class="w200">扩展</th>
                        <th class="wa">描述</th>
                        <th class="w100 txt-ac">操作</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$items item=item}
                    <tr>                        										
                        <td>
                            <strong {if $item.disabled}style="color:gray"{/if}>{$item.Plugin_Name}</strong><br/>
                            {if $item.core}<span class="label label-important">BuildIn</span>{/if}																				
                        </td>
                        <td>
                            <p>{$item.Description}</p>
                            <p class="txt-info" style="margin:5px 0 0 5px;">
                                {$item.curVersion}{if $item.upgradable}(可升级到{$item.Version}){/if}版本| 作者 <a href="{$item.Author_URI}" target="_blank">{$item.Author}</a> |
                                <a href="{$item.Plugin_URI}" target="_blank">访问插件主页</a>
                            </p>
                        </td>
                        <td class="tools">{'get_plugin_operation'|fire:$item}</td>	
                    </tr>
                {foreachelse}
                    <tr><td colspan="4" class="txt-ac">无可用扩展</td></tr>
                {/foreach}
                </tbody>
            </table>
        </div>	                	
    </div>
</div>
{/block}