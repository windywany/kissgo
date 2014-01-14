define('nodes/js/app', function(require, exports) {
	require('jquery/flexigrid');
	var grid;
	exports.main = function(desktop) {
		if (!grid) {
			var colModel = [ {
				display : 'ID',
				name : 'id',
				width : 50,
				sortable : true,
				align : 'center'
			}, {
				display : 'Title',
				name : 'title',
				width : 240,
				sortable : true
			}, {
				display : 'Type',
				name : 'content_type',
				width : 120,
				sortable : true
			}, {
				display : 'Owner',
				name : 'uid',
				width : 120,
				sortable : true
			}, {
				display : 'On Top',
				name : 'ontop',
				width : 120,
				sortable : true
			}, {
				display : 'Status',
				name : 'status',
				width : 80,
				sortable : true,
				align : 'center'
			}, {
				display : 'Commentable',
				name : 'commentable',
				width : 100,
				sortable : true,
				align : 'center'
			}, {
				display : 'Cache Duration',
				name : 'cache_time',
				width : 100,
				sortable : true,
				align : 'center'
			}, {
				display : 'Last Modified',
				name : 'update_time',
				width : 100,
				sortable : true
			}, {
				display : 'Modify User',
				name : 'update_user',
				width : 100,
				sortable : true,
				hide : true
			}, {
				display : 'Create User',
				name : 'create_user',
				width : 100,
				sortable : true,
				hide : true
			}, {
				display : 'Create Time',
				name : 'create_time',
				width : 100,
				sortable : true,
				hide : true
			}, {
				display : 'Author',
				name : 'author',
				width : 100,
				sortable : true,
				hide : true
			}, {
				display : 'Source',
				name : 'source',
				width : 100,
				sortable : true,
				hide : true
			}, {
				display : 'Figure',
				name : 'figure',
				width : 100,
				sortable : true,
				hide : true
			}, {
				display : 'Link',
				name : 'linkto',
				width : 100,
				sortable : true,
				hide : true
			} ];
			var buttons = [ {
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
			} ];
			grid = $('#nodes_grid').flexigrid({
				url : KsgApp.acturl('nodes', 'data'),
				dataType : 'json',
				height : 'auto',
				colModel : colModel,
				sortname : "id",
				sortorder : "desc",
				usepager : true,
				useRp : true,
				rp : 15,
				showTableToggleBtn : false,
				buttons:buttons
			});
		}

	};
});