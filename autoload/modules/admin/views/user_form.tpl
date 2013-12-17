{extends file=$layout}
{block name="layout_style_block"}
<link href="{'jquery/flexigrid/flexigrid.css'|module}"	rel="stylesheet" />
{/block}
{block name="subtitle"}<a href="{$admincp}/admin/users/">用户账户</a> - {$action} {/block}
{block name="workbench"}

<div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#user_frame"><i class="icon-user"></i></a></li>
            </ul>

            <div class="frames">
                <div class="frame" id="user_frame">
						<form action="{$admincp}/admin/users/save/" id="user_form" method="post" class="grid fluid">
						<input type="hidden" name="id" value="{$id}"/>
						<fieldset>
							<div class="row">
									<div class="span2"><label for="username">用户名</label></div>
									<div data-role="input-control" class="input-control text span5">
                                        <input type="text"  tabindex="1" name="username" id="username"/>
                                    </div>
									<div class="span5"><label for="username" class="error tip">只能由字母,数字或下划线组成,登录系统时使用。</label></div>
							</div>
							<div class="row">
									<div class="span2"><label for="display_name">姓名</label></div>
									<div data-role="input-control" class="input-control text span5">
                                        <input type="text"  tabindex="2" name="display_name" id="display_name"/>
                                    </div>
									<div class="span5"><label for="display_name" class="error tip">用于显示的姓名,可以是任意字符.</label></div>
							</div>
							<div class="row">
									<div class="span2"><label for="email">邮箱</label></div>
									<div data-role="input-control" class="input-control text span5">
                                        <input type="text"  tabindex="3" name="email" id="email"/>
                                    </div>
									<div class="span5"><label for="email" class="error hide"></label></div>
							</div>
							<div class="row">
									<div class="span2"><label for="password">密码</label></div>
									<div data-role="input-control" class="input-control password span5">
                                        <input type="password"  tabindex="4" name="password" id="password"/>
                                    </div>
									<div class="span5"><label for="password" class="error tip">至少6个字符.</label></div>
							</div>
							<div class="row">
									<div class="span2"><label for="status">激活</label></div>
									<div data-role="input-control" class="input-control switch span5">
                                        <label>
                                            <input type="checkbox"  checked="checked" name="status"  id="status"tabindex="5"/>
                                            <span class="check"></span>
                                        </label>
                                    </div>
									<div class="span5"></div>
							</div>
							<div class="row">
								<div class="span2"></div>
								<div class="span5">
									<button class="button large success"><i class="icon-floppy on-left"></i>保存</button>
								</div>
							</div>
						</fieldset>
						</form>
                </div>
            </div>
</div>

{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use('admin/js/user_form', function(app) {
            $(function(){
            	var validateRule = {$validateRule};
            	app.main(validateRule);
            });
        });
</script>
{/block}