{extends file="kissgo/admincp_no_sidebar.tpl"}
{block name="title"}Setup Extensions{/block}
{block name="breadcrumb"}
	<li><a href="{$_page_url}">{'Extensions'|ts}</a><span class="divider">/</span></li>
	<li>{'Setup Extension'|ts} - {$extName}</li>
{/block}
{block name="admincp_body"}
<div class="tabbable tabs-left">
    <ul class="nav nav-tabs" style="min-height:385px;margin-top:50px;">
	    <li>&nbsp;</li>						
		<li>
		    <a class="tgre" href="{$_page_url}"><i class="icon-check"></i>已安装</a>
		</li>						
		<li>
		    <a href="{$_page_url}?group=uninstall"><i class="icon-inbox"></i>未安装</a>
		</li>
		<li class="active">
		    <a href="#"><i class="icon-hdd"></i>{$op_title}配置</a>
		</li>					
	</ul>

    <div class="tab-content" style="padding-top:10px;">	                	
	    <div class="tab-pane active">
	        <h4>{$op_title}"{$extName}"扩展</h4>           
            <form id="install-form" action="{'kissgo'|murl:extension}" method="post">        
                {$form}
                <input type="hidden" name="setup" value="{$pid}"/>
                <input type="hidden" name="type" value="{$type}"/>
                <input type="hidden" name="done" value="1"/>
                <input type="hidden" name="op" value="{$operation}"/>
            </form>
            <div class="txt-ac">
                <button class="btn" style="margin-right:15px;" id="cancle-btn">重置</button>
        	    <button class="btn btn-primary" id="install-btn">{$op_title}</button>        	    
            </div>
        </div>        
    </div>
</div>
<script type="text/javascript">
	$(function(){
		$('#install-form').uvalidate();
		$('#install-btn').click(function(){			
			$('#install-form').submit();			
		});
		$('#cancle-btn').click(function(){
			$('#install-form').resetForm();	
		});
	});	
</script>
{/block}