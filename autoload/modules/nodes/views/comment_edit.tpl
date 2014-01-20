<form action="{$admincp}/nodes/comment/save/{$cmt.id}/" method="POST" id="edit-cmt-form" style="margin-bottom:0">
	<input type="hidden" name="apid" value="{$cmt.approved_uid}"/>
    <div class="grid fluid" style="margin-bottom:0">
		<div class="row" style="margin-top:0">
			<div class="span1"><label>作者</label></div>
			<div class="input-control text span11"><input type="text" id="ipt-author"  value="{$cmt.author}" name="author"/></div>
		</div>
		<div class="row" style="margin-top:2px">
			<div class="span1"><label>主页</label></div>
			<div class="input-control text span5"><input type="text" id="ipt-author-url"  value="{$cmt.author_url}" name="url"/></div>
			<div class="span1"><label>邮箱</label></div>
			<div class="input-control text span5"><input type="text" id="ipt-author-mail"  value="{$cmt.author_email}" name="mail"/></div>
		</div>
		<div class="row" style="margin-top:2px">
			<div class="span1"><label>主题</label></div>
			<div class="input-control text span11"><input type="text" id="ipt-author-subject"  value="{$cmt.subject}" name="subject"/></div>
		</div>
		<div class="row" style="margin-top:2px">
			<div class="span1"><label>状态</label></div>
			<div class="input-control text span10">
				<div class="input-control radio" data-role="input-control">
                        <label class="fg-green">已审核
                            <input type="radio"  value="pass" name="status" {if $cmt.status=='pass'}checked="checked"{/if}/>
                            <span class="check"></span>
                        </label>
                    </div>
                    <div class="input-control radio" data-role="input-control">
                        <label class="fg-orange">待审核
                            <input type="radio"  value="new" name="status" {if $cmt.status=='new'}checked="checked"{/if}/>
                            <span class="check"></span>
                        </label>
                    </div>
                    <div class="input-control radio" data-role="input-control">
                        <label class="fg-red">垃圾评论
                            <input type="radio"  value="spam" name="status" {if $cmt.status=='spam'}checked="checked"{/if}/>
                            <span class="check"></span>
                        </label>
                    </div>
			</div>
		</div>
    </div>
    <div id="edit-cmt-wrapper">
    	<textarea id="edit-cmt-txtr" class="quicktags-editor" rows="5" name="content">{$cmt.content}</textarea>
    </div>
    <div></div>
    <div class="form-actions">
    	<button class="button primary">确定</button>&nbsp;
    	<button class="button" type="button">取消</button>
    </div>
</form>