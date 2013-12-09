define('admin/js/users', function(require, exports) {
	require('jquery/flexigrid');
	var grid;
	exports.main = function() {
		if (!grid) {
			var colModel = [ {
				display : 'ID',
				name : 'id',
				width : 50,
				sortable : true,
				align : 'center'
			} ];
			grid = $('#users_grid').flexigrid({
				url : KsgApp.acturl('admin', 'users_data'),
				dataType : 'json',
				colModel : colModel,
				sortname : "id",
				sortorder : "desc",
				usepager : true,
				useRp : true,
				rp : 15,
				showTableToggleBtn : false
			});
		}
	}
});