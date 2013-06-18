{extends file=$ksg_admincp_layout}
{block name="title"}{'Navigation Menus'|ts}{/block}
{block name="breadcrumb"}
	<li>{'Navigation Menus'|ts}</li>
{/block}
{block name="admincp_css_block"}
<link href="{'jquery/ui/smoothness/jquery-ui.css'|static}" rel="stylesheet"/>
<link href="{'menu.css'|here}" rel="stylesheet"/>
{/block} 
{block name="admincp_body"}

<div class="workspace">			
            <div id="pagestuff">            	
                <div id="side-info-column" class="sidebar">
                    <div id="side-sortables">
                        <div class="stuffbox">
                            <div class="handlediv" title="点击以切换"><br></div>
                            <h3 class="hndle"><span>自定义链接</span></h3>  
                            <div class="inside">                                    
                                <div class="form-inline">
                                    <div class="input-prepend mgb5">
                                        <span class="add-on">名称</span><input type="text" id="n_item_name" class="w200" value=""/>
                                    </div>
                                    <br class="clear"/>
                                    <div class="input-prepend mgt5">
                                        <span class="add-on">提示</span><input type="text" id="n_title" class="w200" value=""/>
                                    </div>
                                    <br class="clear"/>
                                    <div class="input-prepend mgt5">
                                        <span class="add-on">URL</span><input type="text" id="n_url" class="w200" value=""/>
                                    </div>
                                    <br class="clear"/>
                                    <div class="mgt5">
	                                    <label class="radio"><input type="radio" class="n_target" value="_blank" name="n_target"/>新窗口</label>
	                                    <label class="radio"><input type="radio" class="n_target" value="_self" name="n_target" checked="checked"/>原窗口</label>
	                                    <button id="btn-add-url2menu" class="btn btn-add2-menu pull-right">添加至菜单</button>
	                                    <br class="clear"/>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        
                        <div class="stuffbox">
                            <div class="handlediv" title="点击以切换"><br></div>
                            <h3 class="hndle"><span>页面</span></h3>  
                            <div class="inside">                                    
                                <div class="form-inline">
                                	<div class="tabbable tabs-below">
								        <div class="tab-content" id="pages-wrapper">
								          <div id="page-A" class="tab-pane active">
								            {foreach $npages as $item}
								            <p>
								            	<label class="checkbox"><input type="checkbox" class="npage" value="{$item.nid}" title="{$item.title}"/>{$item.title|truncate:30}</label>
								            </p>
								            {foreachelse}
								            <p>暂无可用页面</p>
								            {/foreach}
								          </div>
								          <div id="page-B" class="tab-pane">
								            <input type="hidden" id="autoc-id" value=""/>
								            <div class="input-prepend">
		                                        <span class="add-on">条件</span><input type="text" id="page-autoc" autocomplete="off" class="w180"/>
		                                    </div>
								          </div>								          
								        </div>
								        <ul class="nav nav-tabs">
								          <li class="active"><a data-toggle="tab" href="#page-A">常用</a></li>
								          <li><a data-toggle="tab" href="#page-B">搜索</a></li>								          
								        </ul>
								    </div>
                                    <div>
	                                    <label class="radio"><input type="radio" class="n_target" name="n_p_target" value="_blank"/>新窗口</label>
	                                    <label class="radio"><input type="radio" class="n_target" name="n_p_target" value="_self" checked="checked"/>原窗口</label>                                    
	                                    <button id="add-page2-menu" class="btn btn-add2-menu pull-right">添加至菜单</button>
	                                    <br class="clear"/>
                                    </div>
                                </div>
                            </div>
                        </div>                                               
                    </div>
                    {if $op =='add'}
                    <div class="sidebar-overlay"></div>
                    {/if}
                </div>
                <div id="page-body">
                    <div class="post-body-form">
                        <div class="tabbable tabs-left autoset">
                            <ul class="nav nav-tabs" style="min-height:500px;">
                            <li>&nbsp;&nbsp;</li>                                
                                {foreach $menus as $item}
                                    <li class="{if $item.menu_id == $menu_id}active{/if}">                                    	
                                    	<a href="{$_CUR_URL}?mid={$item.menu_id}">
                                    		{if $item.menu_default}<i class="icon-star"></i>{/if}
                                    		{$item.menu_title}({$item.menu_name})                                    		
                                    	</a>
                                    </li>
                                {/foreach}
                                <li class="{if $op=='add'}active{/if}"><a href="{$_CUR_URL}"><i class="icon-plus"></i>新增菜单</a></li>
                            </ul>
                            <div class="tab-content">                                
                                {if $op =='add'}
                                    <div class="well1">
                                        <form action="{$_CUR_URL}" method="POST" class="form-inline">
                                            <div class="well-head">
                                                <div class="mgb5">
                                                    <div class="input-prepend">
                                                        <span class="add-on">引用名</span><input type="text" class="span2" name="menu_name" value="{$menu.menu_name}" placeholder="菜单引用名"/>
                                                    </div>
                                                    <div class="input-prepend">
                                                        <span class="add-on">名称</span><input type="text" class="span2" name="menu_title" value="{$menu.menu_title}" placeholder="菜单名称"/>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary pull-right"><i class="icon-check"></i>创建菜单</button>
                                                    <br class="clear"/>
                                                </div>
                                            </div>
                                            <div class="well-body">
                                                <div class="post-body-plain">
                                                    要创建一个自定义菜单，为它起一个名字并设置引用名以便在模板中引用，并点击“创建菜单”。然后选择要加入菜单的项目（页面、分类目录、自定义链接等）。添加完菜单项后，使用拖放的方式来对它们进行排序。您可以点击它们，进行更详细的设置。
                                                                                                                        当您完成自定义菜单的构建后，不要忘记点击“保存菜单”按钮。
                                                </div>
                                            </div>
                                            <div class="well-foot">                                                
                                                <button type="submit" class="btn btn-primary pull-right"><i class="icon-check"></i>创建菜单</button>
                                                <br class="clear"/>
                                            </div>
                                        </form>
                                    </div>
                                {else}  
                                     <div class="well1">
                                        <form action="{$_CUR_URL}" method="POST" class="form-inline" id="edit-menuitem">
                                            <div class="well-head">
                                                <div class="mgb5">
                                                    <input type="hidden" id="menu_id" name="menu_id" value="{$menu.menu_id}"/>
                                                    <div class="input-prepend">
                                                        <span class="add-on">引用名</span><input type="text" id="menu_name" class="span2" name="menu_name" value="{$menu.menu_name}" readonly="readonly"/>
                                                    </div>
                                                    <div class="input-prepend">
                                                        <span class="add-on">名称</span><input type="text" class="span2" name="menu_title" value="{$menu.menu_title}" placeholder="菜单名称"/>
                                                    </div>
                                                    <label class="radio"><input type="radio" name="menu_default" {'1'|checked:$menu.menu_default}/>默认</label>                                                    
                                                    <button type="submit" class="btn-small btn-primary pull-right mgl5"><i class="icon-check"></i> 保存</button>
                                                    <a href="{$_CUR_URL}/del?mn={$menu.menu_name}" class="btn-small btn-danger pull-right" style="margin-right:10px;"><i class="icon-trash"></i> 删除</a>
                                                    <br class="clear"/>
                                                </div>
                                            </div>
                                            <div class="well-body">
                                                {if $items}
                                                 <ol id="menuitem-list" class="sortable">
                                                 	{'output_menu_items'|fire:$items}
                                                 </ol>
                                                {else}
                                                	<ol id="menuitem-list" class="sortable"></ol>
                                                    <div class="post-body-plain" id="menu-instructions"><p>从右侧挑选一些项目（页面、分类目录、链接等）来开始构建您的自定义菜单。</p></div>
                                                {/if}                                                     
                                            </div>
                                            <div class="well-foot">                                                
                                                <button type="submit" class="btn btn-primary pull-right"><i class="icon-check"></i> 保存</button>
                                                <br class="clear"/>
                                            </div>
                                        </form>                                                                   
                                     </div>                         
                                {/if}                                
                            </div>
                        </div>                        
                    </div>
                </div>
                <br class="clear"/>
            </div>		      
		</div>
<div id="menuitem-editor" class="modal hide fade" tabindex="-1" data-width="350" data-backdrop="static" data-keyboard="false">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>编辑菜单项</h3>
    </div>
    <div class="modal-body">
    
        <div class="form-inline">
                <div class="input-prepend row-fluid">
    	        	<span class="add-on">名称</span><input type="text" class="span10" id="ipt-menu-name"/>
    	     	</div>
    	        <div class="input-prepend mgt5 row-fluid">
    	           	<span class="add-on">提示</span><input type="text" class="span10" id="ipt-menu-title"/>
    	        </div>
    	        <div class="input-prepend mgt5 row-fluid" id="ipt-url-wrap">
    	           	<span class="add-on">URL</span><input type="text" class="span10" id="ipt-menu-url"/>
    	        </div>
    	       	<label class="radio"><input type="radio" name="item_target" value="_blank"/>新窗口</label>
    	        <label class="radio"><input type="radio" name="item_target" value="_self"/>原窗口</label>
        </div>
    
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">Close</button>
        <button type="button" class="btn btn-primary" id="menuitem-editor-done">Done</button>
	</div>
</div>
{/block}
{block name="admincp_foot_js_block"}
<script type="text/javascript" src="{'jquery/jquery-ui.js'|static}"></script>
<script type="text/javascript" src="{'jquery/nestedSortable.js'|static}"></script>
<script type="text/javascript" src="{'menus.js'|here}"></script>
{/block}