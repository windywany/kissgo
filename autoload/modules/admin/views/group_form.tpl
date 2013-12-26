{extends file=$layout}
{block name="subtitle"}<a href="{$admincp}/admin/usergroup/">用户组</a> - {$action} {/block}
{block name="workbench"}

<div class="tab-control mgb5" data-role="tab-control">
            <ul class="tabs">
                <li class="active"><a href="#form_frame"><i class="icon-user"></i></a></li>
            </ul>

            <div class="frames">
                <div class="frame" id="form_frame">
						<form action="{$admincp}/admin/usergroup/save/" id="group_form" method="post" class="grid fluid">
						<input type="hidden" name="gid" id="groupid" value="{$group.gid}"/>
						<fieldset>
							<legend>基本信息</legend>
							<div class="row">
									<div class="span2"><label for="name">用户组</label></div>
									<div data-role="input-control" class="input-control text span5">
                                        <input type="text"  tabindex="1" name="name" id="name" value="{$group.name}"/>
                                    </div>
									<div class="span5"><label for="name" class="error tip">只能由字母,数字或下划线组成。</label></div>
							</div>
							<div class="row">
									<div class="span2"><label for="note">备注</label></div>
									<div data-role="input-control" class="input-control text span5">
                                        <input type="text"  tabindex="2" name="note" id="note"  value="{$group.note}"/>
                                    </div>
									<div class="span5"><label for="note" class="error hide"></label></div>
							</div>
						</fieldset>
						<div class="row">
								<div class="span2"></div>
								<div class="span5">
								    <a class="button large default" tabindex="4" href="{$admincp}/admin/usergroup/"><i class="icon-undo on-left"></i>返回</a>
									<button class="button large success" tabindex="3"><i class="icon-floppy on-left"></i>保存</button>
								</div>
							</div>
						</form>
                </div>
            </div>
</div>

{/block}
{block name="layout_foot_block"}
<script type="text/javascript">
	seajs.use(['admin/js/groups','jquery/form','jquery/blockit','jquery/validate'], function(app) {
            $(function(){
            	var validateRule = {$validateRule};
            	app.form(validateRule);
            });
        });
</script>
{/block}