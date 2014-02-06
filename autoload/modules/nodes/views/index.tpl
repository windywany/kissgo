{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}" rel="stylesheet" />
<link href="{'jquery/css/contextmenu.css'|module}" rel="stylesheet" />
{/block}
{block name="subtitle"}页面{/block}
{block name="workbench"}
			    
	    <div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#search_form" data-status="explorer"><i class="icon-monitor-2"></i>浏览</a></li>
                <li><a href="#search_form" data-status="n"><i class="icon-earth fg-orange"></i> 未发布</a></li>
                <li><a href="#search_form" data-status="p"><i class="icon-earth fg-green"></i> 已发布</a></li>                
                <li class="place-right"><a href="#search_form" data-status="trush"><i class="icon-remove fg-red"></i> 回收站</a></li>                               
            </ul>
             
            <div class="frames">
                <div class="frame" id="search_form">
                <form id="node_search_form" class="grid fluid" >
					<input type="hidden" id="status" name="status" value=""/>
					<div class="row">
						<div class="span1"><label for="key">关键词</label></div>
                        <div data-role="input-control" class="span3 input-control text"><input type="text"  tabindex="2" name="key" id="key"/></div>
						<div class="span1"><label for="type">类型</label></div>
						<div data-role="input-control" class="span2 input-control select"><select tabindex="4" name="type" id="type">{html_options options=$types}</select></div>
						<div class="span1"><label for="sd">日期</label></div>
						<div class="span2 input-control text datepicker"><input type="text"  tabindex="3" name="sd" id="sd"/><button type="button" class="btn-date"></button></div>
						<div class="span2 input-control text datepicker"><input type="text"  tabindex="4" name="ed" id="ed"/><button type="button" class="btn-date"></button></div>
					</div>
					<div class="row">
						<div class="span1"><label for="ipt-path">目录</label></div>
						<div class="span10">
							<div data-role="input-control" class="input-control text"><input type="text" tabindex="5" name="path" id="ipt-path"/></div></div>
					    <div class="span1">
							<button class="button primary">确定</button>
						</div>
					</div>
				</form>
                </div>
            </div>
        </div>
	
	<div id="nodes_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use('nodes/js/node', function(app) {
            $(function(){
            	app.main();
            });        	
        });
</script>
{/block}