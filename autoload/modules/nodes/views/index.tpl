{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/pqgrid/pqgrid.css'|module}"	rel="stylesheet" />
<link href="{'jquery/pqgrid/themes/Office/pqgrid.css'|module}"	rel="stylesheet" />
{/block}
{block name="subtitle"}页面{/block}
{block name="workbench"}
	<div id="nodes_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use('nodes/js/app', function(app) {
            $(function(){
            	app.main();
            });        	
        });
</script>
{/block}