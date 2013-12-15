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
			}, {
				display : 'User Name',
				name : 'username',
				width : 200,
				sortable : true
			}, {
				display : 'Display Name',
				name : 'display_name',
				width : 200,
				sortable : true
			}, {
				display : 'Group',
				name : 'gid',
				width : 180,
				sortable : true
			}, {
				display : 'Email',
				name : 'email',
				width : 250,
				sortable : true
			}, {
				display : 'Status',
				name : 'status',
				width : 80,
				sortable : true
			}, {
				display : 'Last Log-in IP',
				name : 'last_ip',
				hide : true,
				width : 180
			}, {
				display : 'Last Log-in Time',
				name : 'last_time',
				width : 120,
				sortable : true
			} ];
			grid = $('#users_grid').flexigrid({
				url : KsgApp.acturl('admin', 'users_data'),
				dataType : 'json',
				colModel : colModel,
				height : 380,
				sortname : "id",
				sortorder : "desc",
				usepager : true,
				useRp : true,
				rp : 15,
				showTableToggleBtn : false,				
				buttons : [ {
					name : 'Add',
					bclass : 'icon-plus fg-green',
					onpress : function() {
					}
				}, {
					name : 'Delete',
					bclass : 'delete',
					onpress : function() {
					}
				}, {
					separator : true
				} ]
			});
		}
	}
});