$(function() {	
	var pks = {};
	var indexes = {};	
	$('#cmb-definition-add').click(
			function() {// add field, index or primary key
				var action = $(this).parent().find('.tab-content').find(
						'.active').attr('id');
				if(action == 'cmb-fields'){
					$('#field-editor').modalmanager().modal();
				}
			});	
	$('#field-editor').on('hidden',function(){		
		$('#fe_id').val('');
		$('#fe_field').val('');		
		$('#fe_type').val('');
		$('#fe_default').val('');
		$('#fe_length').val('');
		$('#fe_nn').removeAttr('checked');
		$('#fe_unsigned').removeAttr('checked');
		$('#fe_comment').val('');
	});
	
	$('#cmb-definition-tabs').on('show', 'a[data-toggle="tab"]', function(e) {
		var tab = $(e.target).attr('href');

		if (tab == '#cmb-pk') {// edit primart key

		} else if (tab == '#cmb-idx') {// edit indexes

		}
	});
	
	$('#field-editor-done').click(function(){
		var field = {},row='';		
		field.id= $('#fe_id').val();
		field.field=$('#fe_field').val();
		if(!field.field){
			return false;
		}
		field.type=$('#fe_type').val();
		field.deft = $('#fe_default').val();
		field.length = $('#fe_length').val();
		field.nn = $('#fe_nn').attr('checked');
		field.unsigned=$('#fe_unsigned').attr('checked');
		field.comment=$('#fe_comment').val();		
		
		if(field.id){
			row = $('#cmb-f-row-'+field.id);
		}else{
			field.id = (new Date()).valueOf();
			row = row = $('#cmb-f-row-0').clone().attr('id','cmb-f-row-'+field.id);
			$('#cmb-field-list').append(row);	
			row.show();
		}		
		row.find('.fe-field').text(field.field);
		row.find('.fe-type').text(field.type);
		row.find('.fe-default').text(field.deft);
		row.find('.fe-length').text(field.length);
		row.find('.fe-nn').text(field.nn?'Y':'N')
		row.find('.fe-unsigned').text(field.unsigned?'Y':'N');
		row.find('.fe-comment').text(field.comment);
		row.data('definition',field);
		return false;
	});
	
	$('#cmb-tabs').on('show', 'a[data-toggle="tab"]', function(e) {
		var tab = $(e.target).attr('href');
		if(tab == '#cmb_source'){
			$('#cmb-php-source').text("<?php var $a = 10; \n $b=10;\n $c = $a + $b;\n?>");
			window.prettyPrint && prettyPrint();			
		}
	});
	
	$('#cmb-field-list').on('click','.cmb-f-edit,.cmb-f-delete',function(){
		if($(this).hasClass('cmb-f-edit')){
			var field = $(this).parents('tr').data('definition');
			$('#fe_id').val(field.id);
			$('#fe_field').val(field.field);
			$('#fe_type').val(field.type);
			$('#fe_default').val(field.deft);
			$('#fe_length').val(field.length);
			field.nn?$('#fe_nn').attr('checked',true):$('#fe_nn').removeAttr('checked');
			field.unsigned?$('#fe_unsigned').attr('checked',true):$('#fe_unsigned').removeAttr('checked');
			$('#fe_comment').val(field.comment);
			
			$('#field-editor').modal();
		}else if(confirm('Are you sure?')){
			$(this).parents('tr').remove();
		}
		return false;
	});
});