$(function() {
	$('.edit-type').click(function() {
		var $this = $(this), id = $this.attr('href').replace('#', '#form-inline-');
		$(id).show();
		$this.parents('.has-row-actions').find('.type-tpl').hide();
	});

	$('.btn-ca-att').click(function() {
		var $this = $(this);
		$this.parents('.has-row-actions').find('.form-inline').hide();
		$this.parents('.has-row-actions').find('.type-tpl').show();
	});

	$('.btn-edit-att').click(function() {
		var $this = $(this), pe = $this.parents('.has-row-actions'), form = pe.find('.form-inline'), bca = form.find('.btn-ca-att');
		var id = form.attr('id').replace('form-inline-', ''), tpl = form.find('.tpl').val(), icon = $this.find('i');
		if (!/.+\.tpl$/.test(tpl)) {
			alert('模板扩展名必须是tpl');
			return false;
		}

		$this.attr('disabled', 'disabled');
		icon.addClass('icon-loading-14').removeClass('icon-edit');
		bca.attr('disabled', 'disabled');

		$.ajax({
			url : $('#type-search-form').attr('action'),
			type : 'POST',
			data : {
				id : id,
				tpl : tpl
			},
			success : function(data) {
				if (data.success) {
					pe.find('.type-tpl').text(tpl).show();
					pe.find('.form-inline').hide();
				} else {
					alert(data.msg);
				}
				$this.removeAttr('disabled');
				bca.removeAttr('disabled');
				icon.addClass('icon-ok').removeClass('icon-loading-14');
			}
		});
		return false;
	});
});