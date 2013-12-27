{extends file=$layout}
{block name="layout_style_block"}
	<link href="{'jquery/css/gridster.css'|module}"	rel="stylesheet" />
	{foreach $styles as $style}<link href="{$style}"	rel="stylesheet" />{/foreach}
{/block}
{block name="toolbar"}
	<a href="#" id="cancel-edit-start" class="place-right hide"><i class="icon-cancel fg-red"></i></a>
	<a href="#" id="edit-start-screen" class="place-right"><i class="icon-grid fg-blue"></i></a>
{/block}
{block name="workbench"}
<div id="start-screen" class="gridster">
        <ul>
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs-w"  id="pages">
                	<div class="tile bg-green" >
                        <a class="tile-content icon" href="{'nodes'|admincp}">
                            <i class="icon-folder-2"></i>
                        </a>
                        <div class="brand">
                            <span class="label fg-white">页面</span>
                            <span class="badge bg-orange">12</span>
                        </div>
                    </div>
                </li>
                <li data-row="1" data-col="2" data-sizex="1" data-sizey="1" class="gs-w"  id="comments">
                		<div class="tile bg-yellow">
                            <div class="tile-content icon">
                                <i class="icon-comments-4"></i>
                            </div>
                            <div class="brand">
                                <span class="label fg-white">评论</span>
                                <span class="badge bg-orange">12</span>
                            </div>
                        </div>
                </li>
    			<li data-row="2" data-col="1" data-sizex="1" data-sizey="1" class="gs-w"  id="media">
    				<div class="tile bg-cyan">
                        <div class="tile-content icon">
                            <i class="icon-pictures"></i>
                        </div>
                        <div class="brand">
                            <span class="label fg-white">多媒体</span>
                            <span class="badge bg-orange">12</span>
                        </div>
                    </div>
    			</li>
    			<li data-row="1" data-col="4" data-sizex="1" data-sizey="1" class="gs-w"  id="theme">
            			<div class="tile bg-mauve">
                            <a class="tile-content icon" href="{$admincp}/admin/theme/">
                                <i class="icon-newspaper"></i>
                            </a>
                            <div class="brand">
                                <span class="label fg-white">主题模板</span>
                            </div>
                        </div>
                </li>
                <li data-row="2" data-col="4" data-sizex="1" data-sizey="1" class="gs-w" id="menu" >
                    <div class="tile bg-darkIndigo">
                        <a class="tile-content icon" href="{'admin'|admincp:user}">
                            <i class="icon-tree-view"></i>
                        </a>
                        <div class="brand">
                            <span class="label fg-white">导航菜单</span>
                        </div>
                    </div>
                </li>

                <li data-row="1" data-col="6" data-sizex="1" data-sizey="1" class="gs-w"  id="adminuser">
    				<div class="tile bg-lightOrange">
                        <a class="tile-content icon" href="{'admin'|admincp:user}">
                            <i class="icon-user-3"></i>
                        </a>
                        <div class="brand">
                            <span class="label fg-white">用户账户</span>
                            <span class="badge bg-orange">{$userTotal}</span>
                        </div>
                    </div>
    			</li>

    			<li data-row="1" data-col="7" data-sizex="1" data-sizey="1" class="gs-w"  id="adminuser">
    				<div class="tile bg-darkCyan">
                        <a class="tile-content icon" href="{'admin'|admincp:usergroup}">
                            <i class=" icon-user"></i>
                        </a>
                        <div class="brand">
                            <span class="label fg-white">用户组</span>
                        </div>
                    </div>
    			</li>

				<li data-row="2" data-col="6" data-sizex="1" data-sizey="1" class="gs-w"  id="content-type">
            			<div class="tile bg-mauve">
                            <a class="tile-content icon" href="{$admincp}/nodes/type/">
                                <i class="icon-files"></i>
                            </a>
                            <div class="brand">
                                <span class="label fg-white">内容模型</span>
                            </div>
                        </div>
                </li>

       </ul>
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