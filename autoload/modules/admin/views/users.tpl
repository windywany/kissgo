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
                <div class="frame" id="search_form">
					<form id="user_search_form" class="grid fluid" >
						<div class="row" >
							<div class="span1"><label for="username">用户名</label></div>
							<div data-role="input-control"  class="span3 input-control text"><input type="text"  tabindex="1" name="username" id="username" /></div>
							<div class="span1"><label for="email">邮箱</label></div>
							<div data-role="input-control"  class="span3 input-control text"><input type="text"  tabindex="2" name="email" id="email" /></div>
							<div class="span1"><label for="gid">用户组</label></div>
							<div data-role="input-control"  class="span3 input-control select"><select name="gid" id="gid">{html_options options=$groups}</select></div>
						</div>
						<div class="row">
							<div class="span1"><label for="display_name">姓名</label></div>
							<div data-role="input-control"  class="span3 input-control text"><input type="text"  tabindex="2" name="display_name" id="display_name" /></div>
							<div class="span1"><label for="status">状态</label></div>
							<div class="span7">
								<div data-role="input-control" class="input-control radio">
                                    <label>
                                        全部
                                        <input type="radio"  name="status" value="" checked="checked"/>
                                        <span class="check"></span>
                                    </label>
                                </div>
                                <div data-role="input-control" class="input-control radio">
                                    <label>
                                        激活
                                        <input type="radio"  name="status" value="1"/>
                                        <span class="check"></span>
                                    </label>
                                </div>
                                <div data-role="input-control" class="input-control radio">
                                    <label>
                                        禁用
                                        <input type="radio"  name="status" value="0"/>
                                        <span class="check"></span>
                                    </label>
                                </div>
							</div>
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
<div id="users_grid"></div>
{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['admin/js/users','jquery/flexigrid'], function(app) {
            $(function(){
            	app.main();
            });
        });
</script>
{/block}