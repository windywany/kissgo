{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}"	rel="stylesheet" />
{/block}
{block name="subtitle"}多媒体{/block}

{block name="workbench"}
<div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#search_form"><i class="icon-search"></i>过滤</a></li>
                <li><a href="#search_form"><i class="icon-search"></i>上传</a></li>
            </ul>

            <div class="frames">
                <div class="frame" id="search_form">
					<form id="media_search_form" class="grid fluid" >
						<div class="row" >
							<div class="span1"><label for="filename">文件名</label></div>
							<div data-role="input-control"  class="span3 input-control text"><input type="text"  tabindex="1" name="filename" id="filename" /></div>
							<div class="span1"><label for="email">上传日期</label></div>
							<div class="span2 input-control text datepicker"><input type="text"  tabindex="2" name="sd" id="sd"/><a class="btn-date"></a></div>
							<div class="span2 input-control text datepicker"><input type="text"  tabindex="3" name="ed" id="ed"/><a class="btn-date"></a></div>
							<div class="span1"><label for="gid">类型</label></div>
							<div data-role="input-control" class="span2 input-control select"><select tabindex="4" name="gid" id="gid">{html_options options=$groups}</select></div>
						</div>
						<div class="row">
							<div class="span3">
								<button class="button primary">确定</button>
							</div>
						</div>
					</form>
                </div>
            </div>
</div>
<div id="medias_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['media/js/media','jquery/flexigrid'], function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}