$(function() {
	var fields = [], idxes = [];
	// add field, index or primary
	$('#cmb-definition-add').click(function() {
		var action = $(this).parent().find('.tab-content').find('.active').attr('id');
		if (action == 'cmb-fields') {
			$('#field-editor').modal();
		} else if (action == 'cmb-idx') {
			$('#idx-editor').modal();
		}
	});
	// clear field-editor when close field editor.
	$('#field-editor').on('hidden', function() {
		$('#fe_id').val('');
		$('#fe_field').val('');
		$('#fe_type').val('');
		$('#fe_default').val('');
		$('#fe_length').val('');
		$('#fe_nn').removeAttr('checked');
		$('#fe_unsigned').removeAttr('checked');
		$('#fe_ai').removeAttr('checked');
		$('#fe_au').removeAttr('checked');
		$('#fe_comment').val('');
		$('#fe_enum_values').val('');
	});
	$('#idx-editor').on('hidden', function() {
		$('#ie_id').val('');
		$('#ie_name').val('');
		$('#ie_type').val('');
		$('#ie_fields').val('').select2("val", "");
	});
	// switch 'field', 'primary key' and 'index' tab
	$('#cmb-definition-tabs').on('show', 'a[data-toggle="tab"]', function(e) {
		var tab = $(e.target).attr('href');
		if (tab == '#cmb-idx') {// edit indexes
			$('#ie_fields').val('');
			scan_fields();
			$('#ie_fields').select2({
				multiple : true,
				data : fields
			});
		}
	});
	// switch 'design' and 'source' tab
	$('#cmb-tabs').on('show', 'a[data-toggle="tab"]', function(e) {
		var tab = $(e.target).attr('href');
		if (tab == '#cmb_source') {
			scan_fields();
			var mn = $('#model_name').val(), mt = $('#model_table').val(), md = $('#model_desc').val();
			var code = [];
			code.push('<?php');
			code.push("class " + mn + "Table extends DbTable {");
			code.push("    var $table = '" + mt + "';");
			code.push("    public function schema() {");
			code.push("        $schema = new DbSchema ( '" + md.replace(/'/g, "\'") + "' );");
			var i_str, idx, idx_name;
			for ( var ix in idxes) {
				idx = idxes[ix];
				i_str = idx.fields.replace(/,/g, "','");
				idx_name = idx.name.length > 0 ? idx.name : idx.fields.replace(/,/g, "_");
				if (idx.type == 'primary') {
					code.push("        $schema->addPrimarykey (array('" + i_str + "'));");
				} else if (idx.type == 'unique') {
					code.push("        $schema->addUnique ('UDX_" + idx_name + "' , array('" + i_str + "'));");
				} else if (idx.type == 'normal') {
					code.push("        $schema->addIndex ('IDX_" + idx_name + "' , array('" + i_str + "'));");
				}
			}
			var f_str, fds, types;
			for ( var f in fields) {
				fds = fields[f].field;
				types = fds.type.split(':');
				f_str = [];
				f_str.push("'type' => '" + types[0] + "'");
				f_str.push("'extra' => '" + types[1] + "'");
				if (fds.length.length > 0) {
					f_str.push(" Idao::LENGTH => " + fds.length);
				}
				if (fds.nn) {
					f_str.push("Idao::NN");
				}
				if (fds.unsigned) {
					f_str.push("Idao::UNSIGNED");
				}
				
				if (fds.ai) {
					if(types[0] == 'int'){
						f_str.push("Idao::AUTOINSERT_UID");
					}else{
						f_str.push("Idao::AUTOINSERT_DATE");
					}					
				}
				if (fds.au) {
					if(types[0] == 'int'){
						f_str.push("Idao::AUTOUPDATE_UID");
					}else{
						f_str.push("Idao::AUTOUPDATE_DATE");
					}					
				}
				if(fds.enum_values.length>0){
					f_str.push(" Idao::ENUM_VALUES => \"" + fds.enum_values+"\"");
				}
				if (fds.deft.length > 0) {
					f_str.push(" Idao::DEFT => " + fds.deft);
				}
				if (fds.comment.length > 0) {
					f_str.push(" Idao::CMMT => '" + fds.comment.replace(/'/g, "\'") + "'");
				}
				code.push("        $schema['" + fds.field + "'] = array(" + f_str.join(",") + ");");
			}
			code.push("        return $schema;");
			code.push("    }");
			code.push("}");
			$('#cmb-php-source').text(code.join("\n"));
			window.prettyPrint && prettyPrint();
		}
	});
	// add or edit a field
	$('#field-editor-done').click(function() {
		var field = {}, row = '';
		field.id = $('#fe_id').val();
		field.field = $('#fe_field').val();
		if (!field.field) {
			return;
		}
		field.type = $('#fe_type').val();
		field.deft = $('#fe_default').val();
		field.length = $('#fe_length').val();
		field.nn = $('#fe_nn').attr('checked');
		field.unsigned = $('#fe_unsigned').attr('checked');
		field.enum_values = $('#fe_enum_values').val();
		if(!field.enum_values){
			field.enum_values = '';
		}
		field.ai = $('#fe_ai').attr('checked');
		field.au = $('#fe_au').attr('checked');
		field.comment = $('#fe_comment').val();
		
		if (field.id) {
			row = $('#cmb-f-row-' + field.id);
		} else {
			field.id = (new Date()).valueOf();
			row = $('#cmb-f-row-0').clone().attr('id', 'cmb-f-row-' + field.id);
			$('#cmb-field-list').append(row);
			row.show();
		}
		row.find('.fe-field').text(field.field);
		row.find('.fe-type').text(field.type);
		row.find('.fe-default').text(field.deft);
		row.find('.fe-length').text(field.length);
		row.find('.fe-nn').text(field.nn ? 'Y' : 'N');
		row.find('.fe-ai').text(field.ai ? 'Y' : 'N');
		row.find('.fe-au').text(field.au ? 'Y' : 'N');
		row.find('.fe-unsigned').text(field.unsigned ? 'Y' : 'N');
		row.find('.fe-comment').text(field.comment);
		row.data('definition', field);
	});
	// edit or delete a field.
	$('#cmb-field-list').on('click', '.cmb-f-edit,.cmb-f-delete', function() {
		if ($(this).hasClass('cmb-f-edit')) {
			var field = $(this).parents('tr').data('definition');
			$('#fe_id').val(field.id);
			$('#fe_field').val(field.field);
			$('#fe_type').val(field.type);
			$('#fe_default').val(field.deft);
			$('#fe_length').val(field.length);
			$('#fe_enum_values').val(field.enum_values);
			field.nn ? $('#fe_nn').attr('checked', true) : $('#fe_nn').removeAttr('checked');
			field.unsigned ? $('#fe_unsigned').attr('checked', true) : $('#fe_unsigned').removeAttr('checked');
			field.ai ? $('#fe_ai').attr('checked', true) : $('#fe_ai').removeAttr('checked');
			field.au ? $('#fe_au').attr('checked', true) : $('#fe_au').removeAttr('checked');			
			$('#fe_comment').val(field.comment);
			$('#field-editor').modal();
		} else if (confirm('Are you sure?')) {
			$(this).parents('tr').remove();
		}
		return false;
	});
	// edit or add an index
	$('#idx-editor-done').click(function() {
		var idx = {}, row = '';
		idx.id = $('#ie_id').val();
		idx.type = $('#ie_type').val();
		idx.name = $('#ie_name').val();
		idx.fields = $('#ie_fields').val();
		if (!idx.fields) {
			return;
		}
		if (idx.id) {
			row = $('#cmb-idx-row-' + idx.id);
		} else {
			idx.id = (new Date()).valueOf();
			row = $('#cmb-idx-row-0').clone().attr('id', 'cmb-idx-row-' + idx.id);
			$('#cmb-idx-list').append(row);
			row.show();
		}
		row.find('.idx-name').text(idx.name);
		row.find('.idx-type').text(idx.type);
		row.find('.idx-fields').text(idx.fields);
		row.data('definition', idx);
	});
	// edit or delete a index.
	$('#cmb-idx-list').on('click', '.cmb-idx-edit,.cmb-idx-delete', function() {
		if ($(this).hasClass('cmb-idx-edit')) {
			var idx = $(this).parents('tr').data('definition'), fds = idx.fields.split(',');
			$('#ie_id').val(idx.id);
			$('#ie_name').val(idx.name);
			$('#ie_type').val(idx.type);
			$('#ie_fields').select2('val', fds);
			$('#idx-editor').modal();
		} else if (confirm('Are you sure?')) {
			$(this).parents('tr').remove();
		}
		return false;
	});
	$('#fe_type').change(function(){
		var v = $(this).val();		
		if(v == 'enum:normal'){
			$('#fe_enum_values_wrapper').removeClass('hidden');
			$('#fe_enum_values').val('');
		}else{
			$('#fe_enum_values_wrapper').addClass('hidden');
		}
	});
	function getFields(fds) {
		var _fs = [], ln = fields.length, k = 0;
		$(fds).each(function(i, e) {
			for ( var j = 0; j < ln; j++) {
				if (fields[j].id == e) {
					_fs[k++] = e;
					break;
				}
			}
		});
		return _fs;
	}
	function scan_fields() {
		var field, j = 0;
		fields = [];
		idxes = [];
		$('#cmb-field-list tr').each(function(i, n) {
			field = $(n).data('definition');
			if (field) {
				fields[j++] = {
					id : field.field,
					text : field.field,
					field : field
				};
			}
		});
		var df, $idx, fds;
		j = 0;
		$('#cmb-idx-list tr').each(function(i, n) {
			$idx = $(n);
			df = $idx.data('definition');
			if (df) {
				fds = getFields(df.fields.split(','));
				if (fds.length == 0) {
					$idx.remove();
				} else {
					df.fields = fds.join(',');
					$idx.data('definition', df).find('.idx-fields').text(df.fields);
					idxes[j++] = df;
				}
			}
		});
	}
});