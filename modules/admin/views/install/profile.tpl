{extends file="$ksg_module/admin/views/install/welcome.tpl"}
{block name="title"}安装类型{/block}
{block name="body"}
<div class="form-horizontal">
	<div class="accordion" id="accordion-profile">
		{foreach from=$profiles item=pf}
		<div class="accordion-group">
            <div class="accordion-heading">
              <label style="padding-left:25px"  class="radio accordion-toggle{if $profile == $pf@key} active{/if}" data-toggle="collapse" data-parent="#accordion-profile" data-target="#profile-{$pf@key}">              	
              	<input class="profile" type="radio" name="profilex" value="{$pf@key}"{if $profile == $pf@key} checked="checked"{/if}/>
              	{$pf.name}
              </label>
            </div>
            <div id="profile-{$pf@key}" class="accordion-body collapse">
              <div class="accordion-inner">{$pf.description}</div>
            </div>
        </div>
        {/foreach}
	</div>	 
</div>
<div class="row">	
	<form class="form-inline pull-right" method="post">
		<input type="hidden" name="step" value="check"/>
		<input type="hidden" name="from" value="profile"/>
		<input type="hidden" id="profile" name="profile" value="{$profile}"/>
		<button type="submit" class="btn btn-primary" id="next-btn"{if empty($profile)} disabled="disabled"{/if}>环境检测&gt;&gt;</button>
	</form>
	<form class="form-inline pull-right mlr10" method="post">
		<input type="hidden" name="step" value="welcome"/>						   
		<button type="submit" class="btn" id="prev-btn">&lt;&lt;安装协议</button>
	</form>
</div>
<script type="text/javascript">
	$(function(){
		$('input.profile').change(function(){
			$('.accordion-toggle').removeClass('active');
			if($(this).attr('checked')){
				$('#profile').val($(this).val());
				$(this).parents('.accordion-toggle').addClass('active');
				$('#next-btn').removeAttr('disabled');
			}
			return false;
		});
	});
</script>
{/block}