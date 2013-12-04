{extends file=$layout}
{block name="layout_style_block"}
	<link href="{'jquery/css/gridster.css'|module}"	rel="stylesheet" />
{/block}
{block name="workbench"}
<div id="start-screen">
	<div class="tile-group double">
		<div class="tile-group-title">内容</div>
		<a class="tile bg-green" href="/autoload/admincp.php/nodes/" >
            <div class="tile-content icon">
                <i class="icon-files"></i>
            </div>
            <div class="brand">
                <span class="label fg-white">页面</span>
                <span class="badge bg-orange">12</span>
            </div>
        </a>
        <div class="tile bg-yellow">
            <div class="tile-content icon">
                <i class="icon-comments-4"></i>
            </div>
            <div class="brand">
                <span class="label fg-white">评论</span>
                <span class="badge bg-orange">12</span>
            </div>
        </div>
        
        <div class="tile bg-cyan">
            <div class="tile-content icon">
                <i class="icon-pictures"></i>
            </div>
            <div class="brand">
                <span class="label fg-white">多媒体</span>
                <span class="badge bg-orange">12</span>
            </div>
        </div>        
	</div>
	
	<div class="tile-group">
		<div class="tile-group-title">外观</div>
		<div class="tile bg-mauve">
            <div class="tile-content icon">
                <i class="icon-newspaper"></i>
            </div>
            <div class="brand">
                <span class="label fg-white">主题-default</span>                                                         
            </div>
        </div>
        <div class="tile bg-darkIndigo">
            <div class="tile-content icon">
                <i class="icon-tree-view"></i>
            </div>
            <div class="brand">
                <span class="label fg-white">导航菜单</span>
            </div>
        </div>
	</div>            		
</div>
{/block}

{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['jquery/gridster','admin/js/dashboard'], function(gridster,app) {			
            $(function(){
            	app.main();
            });        	
        });
</script>
{/block}