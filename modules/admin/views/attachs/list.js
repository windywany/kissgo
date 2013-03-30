$(function() {
	$('#btn-selectall').click(function() {
        $('#attach-list').uiTable('selectAll');
    });
	$('.datepicker').datepicker({
		'format' : 'yyyy-mm-dd',
		autoclose : true
	}).on('changeDate', function(ev) {
		var date = ev.date, target = $(ev.target).attr('id');
		if (target == 'time1') {
			$('#time2').datepicker('setStartDate', date);
		} else {
			$('#time1').datepicker('setEndDate', date);
		}
	});

	$("a[rel^='prettyPhoto']").prettyPhoto({
		social_tools : ''
	});

	$('.menu-del-attach').click(function() {
		var me = $(this), url = me.attr('href'), ids = $('#attach-list').uiTable('selected');
		if (ids.length == 0) {
			alert('请选择要删除的文件');
			return false;
		}
		if (confirm('删除后不能恢复,你确定要删除所选文件?')) {
			window.location.href = url + ids.join(',');
		}
		return false;
	});
	$('.g_thumb').click(function() {
		var me = $(this);
		href = me.attr('href');
		me.find('i').addClass('icon-loading-14').removeClass('icon-picture');
		$.ajax({
			url : href,
			success : function(data) {
				if (!data.success) {
					alert(data.msg);
				}
				me.find('i').addClass('icon-picture').removeClass('icon-loading-14');
			}
		});
		return false;
	});
	$('.menu-g-thumb').click(function() {
		var me = $(this), url = me.attr('href'), ids = $('#attach-list').uiTable('selected');
		if (ids.length == 0) {
			alert('请选择要生成缩略图的文件');
			return false;
		}
		showWaitMask('正在生成缩略图，这可能需要几分钟时间，请等候...');
		url += ids.join(',');
		$.ajax({
			url : url,
			success : function(data) {
				hideWaitMask();
			}
		});
		return false;
	});
	$('.edit-attach').click(function() {
		var me = $(this), pe = me.parents('.attach-info');
		pe.find('.form-inline').show();
		return false;
	});
	$('.btn-ca-att').click(function() {
		var me = $(this), pe = me.parents('.attach-info');
		pe.find('.form-inline').hide();
		return false;
	});
	$('.btn-edit-att').click(function() {
		var me = $(this), pe = me.parents('.attach-info'), bca = pe.find('.btn-ca-att'), icon = me.find('i');
		var name = pe.find('.a_name').val(), alt = pe.find('.a_alt').val(), url = pe.find('.edit-attach').attr('href');
		me.attr('disabled', 'disabled');
		bca.attr('disabled', 'disabled');
		icon.addClass('icon-loading-14').removeClass('icon-ok');

		$.ajax({
			url : url,
			data : {
				name : name,
				alt : alt
			},
			success : function(data) {
				if (data.success) {
					pe.find('.att_name').text(name);
					pe.find('.form-inline').hide();
				} else {
					alert(data.msg);
				}
				me.removeAttr('disabled');
				bca.removeAttr('disabled');
				icon.addClass('icon-ok').removeClass('icon-loading-14');
			}
		});
	});
});