{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}"	rel="stylesheet" />
<link href="{'jquery/css/contextmenu.css'|module}"	rel="stylesheet" />
<link href="{'css/icons.css'|here}" rel="stylesheet"/>
{/block}
{block name="subtitle"}评论{/block}
{block name="workbench"}
<div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#search_form" data-status="new"><i class="icon-comments-4"></i> 最新评论</a></li>
                <li><a href="#search_form" data-status="pass"><i class="icon-comments-4 fg-green"></i> 审核通过的评论</a></li>
                <li><a href="#search_form" data-status="spam"><i class="icon-comments-4 fg-red"></i> 垃圾评论</a></li>
                <li class="place-right"><a href="#search_form" data-status="trush"><i class="icon-remove fg-red"></i> 回收站</a></li>
            </ul>
            <div class="frames">
                <div class="frame" id="search_form">
					<form id="comment_search_form" class="grid fluid" >
					    <input type="hidden" id="status" name="status" value="new"/>
						<div class="row" >
							<div class="span1"><label for="nid">页面ID</label></div>
							<div data-role="input-control" class="span1 input-control text"><input type="text"  tabindex="1" name="nid" id="nid"/></div>
                            <div class="span1"><label for="key">关键词</label></div>
                            <div data-role="input-control" class="span2 input-control text"><input type="text"  tabindex="2" name="key" id="key"/></div>
							<div class="span1"><label for="sd">日期</label></div>
							<div class="span2 input-control text datepicker"><input type="text"  tabindex="3" name="sd" id="sd"/><button type="button" class="btn-date"></button></div>
							<div class="span2 input-control text datepicker"><input type="text"  tabindex="4" name="ed" id="ed"/><button type="button" class="btn-date"></button></div>
						</div>
						<div class="row">
						    <div class="span1">
								<button class="button primary">确定</button>
							</div>
						</div>
					</form>
                </div>
            </div>
</div>
<div id="comments_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript" src="{'js/quicktags.js'|assets}"></script>
<script type="text/javascript">
	seajs.use(['nodes/js/comments','jquery/blockit','jquery/flexigrid'], function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}
