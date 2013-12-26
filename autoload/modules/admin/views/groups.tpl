{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}"	rel="stylesheet" />
{/block}
{block name="subtitle"}用户组{/block}

{block name="workbench"}
<div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#search_form"><i class="icon-search"></i>过滤</a></li>
            </ul>

            <div class="frames">
                <div class="frame" id="search_form">
					<form id="group_search_form" class="grid fluid" >
						<div class="row" >
							<div class="span1"><label for="name">用户组</label></div>
							<div data-role="input-control"  class="span3 input-control text"><input type="text"  tabindex="1" name="name" id="name" /></div>
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
<div id="groups_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['admin/js/groups','jquery/flexigrid'], function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}