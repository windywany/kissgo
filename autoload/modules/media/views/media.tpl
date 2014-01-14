{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}" rel="stylesheet" />
<link href="{'jquery/plupload/css/queue.css'|module}" rel="stylesheet" />
<link href="{'../css/media.css'|here}" rel="stylesheet" />
{/block}
{block name="subtitle"}多媒体{/block}

{block name="workbench"}
<div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#search_form" id="tab-media-grid"><i class="icon-search"></i>过滤</a></li>
                <li><a href="#upload_form"><i class="icon-upload"></i>上传</a></li>
            </ul>

            <div class="frames">
                <div class="frame" id="upload_form">
                    <form id="upload-from" method="post" action=".">
        			    <div id="uploader"></div>
        				<div id="attach-info">
        					<button type="submit" class="button primary"><i class="icon-floppy"></i> 完成上传</button>
        				</div>
                    </form>
                </div>
                <div class="frame" id="search_form">
					<form id="media_search_form" class="grid fluid" >
						<div class="row" >
							<div class="span1"><label for="filename">文件名</label></div>
							<div data-role="input-control"  class="span3 input-control text"><input type="text"  tabindex="1" name="filename" id="filename" /></div>
							<div class="span1"><label for="sd">上传日期</label></div>
							<div class="span2 input-control text datepicker"><input type="text"  tabindex="2" name="sd" id="sd"/><button type="button" class="btn-date"></button></div>
							<div class="span2 input-control text datepicker"><input type="text"  tabindex="3" name="ed" id="ed"/><button type="button" class="btn-date"></button></div>
							<div class="span1"><label for="type">类型</label></div>
							<div data-role="input-control" class="span2 input-control select"><select tabindex="4" name="type" id="type">{html_options options=$types}</select></div>
						</div>
						<div class="row">
							<div class="span3">
								<button class="button primary"><i class="icon-search"></i> 确定</button>
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
	seajs.use(['media/js/media','jquery/blockit','jquery/flexigrid','jquery/plupload/queue'], function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}