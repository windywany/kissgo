{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}"	rel="stylesheet" />
{/block}
{block name="subtitle"}用户账户{/block}

{block name="workbench"}
<div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#search_form"><i class="icon-search"></i>过滤</a></li>
            </ul>

            <div class="frames">
                <div class="frame" id="search_form"></div>
            </div>
</div>
<div id="users_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use('admin/js/users', function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}